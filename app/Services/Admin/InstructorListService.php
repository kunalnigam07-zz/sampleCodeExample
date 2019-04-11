<?php

namespace App\Services\Admin;

use App\Models\InstructorList;
use Yajra\Datatables\Datatables;
use App\Models\InstructorListUser;

class InstructorListService extends AdminService
{
    protected $columns = [
        'cols' => ['instructors_lists.id', 'instructors_lists.title', 'users.name', 'users.surname', 'instructors_lists.created_at', 'instructors_lists.status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(InstructorList $model, InstructorListUser $instructorlistuser)
    {
        $this->model = $model;
        $this->instructorlistuser = $instructorlistuser;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\InstructorListController@create')]],
            'ajax' => route('lists.dt.list'),
            'labels' => ['ID', 'List Title', 'First Name', 'Last Name', 'Created', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = $this->model->select($this->columns['cols'])->join('users', 'instructors_lists.user_id', '=', 'users.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\InstructorListController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\InstructorListController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('created_at', '{{ DateHelper::showDate($created_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1, 2, 3])
            ->make();
    }

    public function listMembers($id)
    {
        $data = [];
        $users = $this->instructorlistuser->select('id', 'name', 'email')->where('instructors_lists_users.list_id', $id)->orderBy('id', 'DESC')->get();

        foreach ($users as $user) {
            $data[$user->id] = $user->name . ' (' . $user->email . ')';
        }

        return $data;
    }

    public function allLists()
    {
        $data = [];
        $lists = $this->model->with('listOwner')->orderBy('id', 'DESC')->get();

        foreach ($lists as $list) {
            $data[$list->id] = $list->title . ' (' . $list->listOwner->name . ' ' . $list->listOwner->surname . ')';
        }

        return $data;
    }

    public function listCopyUsers($users, $list_id)
    {
        // Get users, add to new list
        $users = $this->instructorlistuser->select('name', 'email')->whereIn('id', $users)->get();

        foreach ($users as $user) {
            $this->instructorlistuser->create(['list_id' => $list_id, 'name' => $user->name, 'email' => $user->email]);
        }
    }

    public function listDeleteUsers($users, $list_id)
    {
        // Delete users from specified list
        $this->instructorlistuser->whereIn('id', $users)->where('list_id', $list_id)->delete();
    }

    public function action($data, $id)
    {
        $users = $data->get('users');
        $list_id = $data->get('list');

        if (is_array($users) && count($users) > 0) {
            switch ($data->get('action')) {
                case 1: // Copy
                    if ($list_id > 0) {
                        $this->listCopyUsers($users, $list_id);

                        return redirect()->action('Admin\InstructorListController@edit', $id)->with('flash_message_success', 'The selected users have been copied.');
                    }
                    break;
                case 2: // Move
                    if ($list_id > 0) {
                        $this->listCopyUsers($users, $list_id);
                        $this->listDeleteUsers($users, $id);

                        return redirect()->action('Admin\InstructorListController@edit', $id)->with('flash_message_success', 'The selected users have been moved.');
                    }
                    break;
                case 3: // Delete
                    $this->listDeleteUsers($users, $id);

                    return redirect()->action('Admin\InstructorListController@edit', $id)->with('flash_message_success', 'The selected users have been deleted.');
                    break;
            }
        }

        return redirect()->action('Admin\InstructorListController@edit', $id)->with('flash_message_error', 'No action completed - please ensure all required fields are selected.');
    }

    public function import($data, $id)
    {
        $num = 0;
        $csv = array_map('str_getcsv', preg_split('/\r*\n+|\r+/', $data->get('data')));

        foreach ($csv as $k => $v) {
            if (count($v) == 2) {
                $num++;
                $this->instructorlistuser->create(['list_id' => $id, 'name' => $v[0], 'email' => $v[1]]);
            }
        }
        
        return redirect()->action('Admin\InstructorListController@edit', $id)->with('flash_message_success', 'Import of ' . ($num == 1 ? '1 user' : $num . ' users') . ' complete.');
    }
}
