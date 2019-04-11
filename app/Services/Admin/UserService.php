<?php

namespace App\Services\Admin;

use Hash;
use App\Models\User;
use Yajra\Datatables\Datatables;

class UserService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'name', 'surname', 'email', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\UserController@create')]],
            'ajax' => route('users.dt.list'),
            'labels' => ['ID', 'First Name', 'Last Name', 'Email', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = User::admins()->select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\UserController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                @if($id <> 1) <a href="{{ action(\'Admin\UserController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a> @endif')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1, 2, 3])
            ->make();
    }

    public function updatePermissions($id, $type, $permissions)
    {
        $final_permissions = [];

        if (!is_array($permissions)) {
            $permissions = [];
        }

        if ($type == 1) {
            $final_permissions = [1];
        } else {
            $temp_permissions = [];
            foreach ($permissions as $k => $v) {
                if (explode('_', $v)[1] != 0) { // Don't allow parents with no children
                    $temp_permissions[] = explode('_', $v)[0];
                    $temp_permissions[] = explode('_', $v)[1];
                }  
            }
            $final_permissions = array_diff(array_unique($temp_permissions), [0]); // Remove the 0 of parent permissions
        }

        $user = $this->getEntry($id);

        $user->permissions()->sync($final_permissions);
    }

    public function editEntry($data, $id)
    {
        $fields = $data->except('_token', 'permissions');

        if (strlen($fields['password']) == 0) {
            unset($fields['password']);
        } else {
            $fields['password'] = Hash::make($fields['password']);
        }

        $this->model->where('id', $id)->update($fields);

        $this->updatePermissions($id, $fields['admin_type'], $data->get('permissions'));

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'permissions');
        $fields['user_type'] = 1;

        if (strlen($fields['password']) == 0) {
            $fields['password'] = Hash::make(str_random(40));
        } else {
            $fields['password'] = Hash::make($fields['password']);
        }

        $new = $this->model->create($fields);

        $this->updatePermissions($new->id, $fields['admin_type'], $data->get('permissions'));

        return true;
    }
}
