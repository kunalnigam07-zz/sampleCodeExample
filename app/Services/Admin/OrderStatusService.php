<?php

namespace App\Services\Admin;

use App\Models\OrderStatus;
use Yajra\Datatables\Datatables;

class OrderStatusService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'title', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '0, "asc"'
    ];

    public function __construct(OrderStatus $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\OrderStatusController@create')]],
            'ajax' => route('statuses.dt.list'),
            'labels' => ['ID', 'Title', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = OrderStatus::select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\OrderStatusController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                @if ($id > 3)
                    <a href="{{ action(\'Admin\OrderStatusController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>
                @endif')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
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
