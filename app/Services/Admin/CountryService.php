<?php

namespace App\Services\Admin;

use App\Models\Country;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use StringHelper;

class CountryService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'title', 'code', 'dialing', 'sanctioned', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '1, "asc"'
    ];

    public function __construct(Country $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\CountryController@create')]],
            'ajax' => route('countries.dt.list'),
            'labels' => ['ID', 'Name', 'Code', 'Dialing', 'Sanctioned?', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = Country::select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\CountryController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\CountryController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('dialing', '+{{ $dialing }}')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->editColumn('sanctioned', '
                @if ($sanctioned == 0)
                    <span class="group green">No</span>
                @else
                    <span class="group red">Yes</span>
                @endif')
            ->escapeColumns([1, 2])
            ->make();
    }
}
