<?php

namespace App\Services\Admin;

use App\Models\Page;
use Yajra\Datatables\Datatables;

class PageService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'title', 'url', 'ordering', 'updated_at', 'user_added', 'status'],
        'exclude' => ['user_added'],
        'order' => '3, "asc"'
    ];

    public function __construct(Page $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\PageController@create')]],
            'ajax' => route('pages.dt.list'),
            'labels' => ['ID', 'Page Title', 'URL', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = $this->model->select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\PageController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                @if($user_added == 1) <a href="{{ action(\'Admin\PageController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a> @endif')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->removeColumn('user_added')
            ->escapeColumns([1, 2])
            ->make();
    }
}
