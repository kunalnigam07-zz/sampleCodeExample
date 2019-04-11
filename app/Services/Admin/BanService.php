<?php

namespace App\Services\Admin;

use App\Models\Ban;
use Yajra\Datatables\Datatables;

class BanService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'ip', 'email', 'bank_account_number', 'paypal_email', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(Ban $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\BanController@create')]],
            'ajax' => route('bans.dt.list'),
            'labels' => ['ID', 'IP', 'Email', 'Account #', 'PayPal', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = Ban::select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\BanController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\BanController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1, 2])
            ->make();
    }
}
