<?php

namespace App\Services\Admin;

use App\Models\ClassType;
use Yajra\Datatables\Datatables;

class ClassTypeService extends AdminService
{
    protected $columns = [
        'cols' => ['classes_types.id', 'classes_types.title', 'ct2.title AS ctitle2', 'ct3.title AS ctitle3', 'classes_types.ordering', 'classes_types.updated_at', 'classes_types.status'],
        'exclude' => [],
        'order' => '4, "asc"'
    ];

    public function __construct(ClassType $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\ClassTypeController@create')]],
            'ajax' => route('classes-types.dt.list'),
            'labels' => ['ID', 'Type', 'Parent', 'Parent', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = ClassType::select($this->columns['cols'])
            ->leftJoin('classes_types AS ct2', 'classes_types.parent_id', '=', 'ct2.id')
            ->leftJoin('classes_types AS ct3', 'ct2.parent_id', '=', 'ct3.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\ClassTypeController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\ClassTypeController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->editColumn('title', '
                {{ $title }} &nbsp; 
                @if (strlen($ctitle2) == 0)
                    <span class="group blue">Primary</span>
                @elseif (strlen($ctitle3) == 0)
                    <span class="group lightblue">Secondary</span>
                @endif')
            //->escapeColumns([1])
            ->make();
    }
}
