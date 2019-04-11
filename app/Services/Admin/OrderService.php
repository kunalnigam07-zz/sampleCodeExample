<?php

namespace App\Services\Admin;

use DateHelper;
use NumberHelper;
use App\Models\Order;
use Maatwebsite\Excel\Excel;
use Yajra\Datatables\Datatables;

class OrderService extends AdminService
{
    protected $columns = [
        'cols' => ['orders.id', 'orders.order_number', 'users.name AS mname', 'users.surname AS msurname', 'orders.price', 'orders.created_at', 'orders_statuses.title AS stitle'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(Order $model, Excel $excel)
    {
        $this->model = $model;
        $this->excel = $excel;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [],
            'ajax' => route('orders.dt.list'),
            'labels' => ['ID', 'Order Number', 'First Name', 'Last Name', 'Price', 'Created', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = Order::select($this->columns['cols'])
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->join('orders_statuses', 'orders.status_id', '=', 'orders_statuses.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\OrderController@edit\', $id) }}" class="edit"><i class="fa fa-search"></i></a>
                <a href="{{ action(\'Admin\OrderController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('created_at', '{{ DateHelper::showDate($created_at) }}')
            ->editColumn('price', '{{ \'£\' . NumberHelper::money($price) }}')
            ->editColumn('stitle', '
                @if ($stitle == \'Paid\')
                    <span class="group green">{{ $stitle }}</span>
                @else
                    <span class="group red">{{ $stitle }}</span>
                @endif')
            ->escapeColumns([1, 2, 3])
            ->make();
    }
}
