<?php

namespace App\Services\Admin;

use DB;
use Carbon\Carbon;
use App\Models\FAQCategory;
use Yajra\Datatables\Datatables;

class FAQCategoryService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'title', 'section_id', 'ordering', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '3, "asc"'
    ];

    public function __construct(FAQCategory $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\FAQCategoryController@create')]],
            'ajax' => route('faqs-categories.dt.list'),
            'labels' => ['ID', 'Category', 'Section', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = $this->model->select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\FAQCategoryController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\FAQCategoryController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('section_id', '{{ $section_id == 1 ? \'Website\' : \'Instructors\' }}') 
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1])
            ->make();
    }
}
