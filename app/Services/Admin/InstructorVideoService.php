<?php

namespace App\Services\Admin;

use App\Models\InstructorVideo;
use Yajra\Datatables\Datatables;

class InstructorVideoService extends AdminService
{
    protected $columns = [
        'cols' => ['instructors_videos.id', 'instructors_videos.url', 'users.name', 'users.surname', 'instructors_videos.ordering', 'instructors_videos.updated_at', 'instructors_videos.status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(InstructorVideo $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\InstructorVideoController@create')]],
            'ajax' => route('videos.dt.list'),
            'labels' => ['ID', 'Video URL', 'First Name', 'Last Name', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = InstructorVideo::select($this->columns['cols'])->join('users', 'instructors_videos.user_id', '=', 'users.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\InstructorVideoController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\InstructorVideoController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
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
}
