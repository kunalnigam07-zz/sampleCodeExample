<?php

namespace App\Services\Admin;

use App\Models\Currency;
use Yajra\Datatables\Datatables;

class CurrencyService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'title', 'code', 'symbol', 'rate', 'profit_rate', 'ordering', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '6, "asc"'
    ];

    public function __construct(Currency $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\CurrencyController@create')]],
            'ajax' => route('currencies.dt.list'),
            'labels' => ['ID', 'Title', 'Code', 'Symbol', 'Rate', 'Margin', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = Currency::select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\CurrencyController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\CurrencyController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
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
