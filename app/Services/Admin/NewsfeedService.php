<?php

namespace App\Services\Admin;

use Carbon;
use UploadHelper;
use App\Models\Newsfeed;
use Yajra\Datatables\Datatables;

class NewsfeedService extends AdminService
{
    protected $folder = 'newsfeed';
    protected $columns = [
        'cols' => ['id', 'contents', 'section_id', 'for_users', 'ordering', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '4, "asc"'
    ];

    public function __construct(Newsfeed $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\NewsfeedController@create')]],
            'ajax' => route('newsfeed.dt.list'),
            'labels' => ['ID', 'Message', 'Section', 'For Users', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = Newsfeed::select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\NewsfeedController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\NewsfeedController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('section_id', '
                @if ($section_id == 1)
                    Newsfeed
                @else
                    Notifications
                @endif')
            ->editColumn('for_users', '
                @if ($for_users == 2)
                    Instructors
                @elseif ($for_users == 3)
                    Members
                @else
                    Both
                @endif')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1])
            ->make();
    }

    public function editEntry($data, $id)
    {
        $fields = $data->except('_token', 'img', 'delete-img');
      
        if ($data->hasFile('img')) {
            $fields['img'] = UploadHelper::upload('img', $data, $this->folder, $id);
        }

        if ($data->has('delete-img')) {
            $fields['img'] = '';
        }
        
        $this->model->where('id', $id)->update($fields);

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'img', 'delete-img');
        
        $new = $this->model->create($fields);

        if ($data->hasFile('img')) {
            $new->img = UploadHelper::upload('img', $data, $this->folder, $new->id);
            $new->save();
        }

        if ($data->has('delete-img')) {
            $new->img = '';
            $new->save();
        }

        return true;
    }

    public function deleteEntry($id)
    {
        UploadHelper::deleteDirectory($this->folder, $id);

        return $this->model->destroy($id);
    }
}
