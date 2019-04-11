<?php

namespace App\Services\Admin;

use App\Models\BulkPackage;
use Yajra\Datatables\Datatables;

class BulkPackageService extends AdminService
{
    protected $columns = [
        'cols' => ['bulk_packages.id', 'bulk_packages.classes_number', 'bulk_packages.price', 'bulk_packages.expiry_days', 'bulk_packages.type', 'users.name', 'users.surname', 'bulk_packages.updated_at', 'bulk_packages.status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(BulkPackage $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\BulkPackageController@create')]],
            'ajax' => route('bulk-packages.dt.list'),
            'labels' => ['ID', 'Classes', 'Price', 'Expiry', 'Type', 'First Name', 'Last Name', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = BulkPackage::select($this->columns['cols'])->join('users', 'bulk_packages.user_id', '=', 'users.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\BulkPackageController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\BulkPackageController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('price', '{{ \'&pound;\' . $price }}')
            ->editColumn('type', '{{ $type == 0 ? \'1-on-1\' : \'Group\' }}')
            ->editColumn('expiry_days', '{{ $expiry_days . \' Days\' }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([5, 6])
            ->make();
    }
}
