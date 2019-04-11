<?php

namespace App\Services\Admin;

use App\Models\DiscountCodeRedemption;
use Yajra\Datatables\Datatables;

class DiscountCodeRedemptionService extends AdminService
{
    protected $columns = [
        'cols' => ['discount_codes_redemptions.id', 'users.name AS uname', 'users.surname AS usurname', 'discount_codes.title AS dtitle', 'discount_codes_redemptions.updated_at'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(DiscountCodeRedemption $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [],
            'ajax' => route('discount-code-redemptions.dt.list'),
            'labels' => ['ID', 'First Name', 'Last Name', 'Discount', 'Date', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = DiscountCodeRedemption::select($this->columns['cols'])
            ->join('users', 'discount_codes_redemptions.user_id', '=', 'users.id')
            ->join('discount_codes', 'discount_codes_redemptions.discount_id', '=', 'discount_codes.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\DiscountCodeRedemptionController@edit\', $id) }}" class="edit"><i class="fa fa-search"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->escapeColumns([1, 2])
            ->make();
    }
}
