<?php

namespace App\Services\Admin;

use Auth;
use Carbon;
use StringHelper;
use App\Models\DiscountCode;
use Maatwebsite\Excel\Excel;
use Yajra\Datatables\Datatables;

class DiscountCodeService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'title', 'code', 'type', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(DiscountCode $model, Excel $excel)
    {
        $this->model = $model;
        $this->excel = $excel;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\DiscountCodeController@create')], ['Export', 'file-excel-o', action('Admin\DiscountCodeController@showExport')]],
            'ajax' => route('discount-codes.dt.list'),
            'labels' => ['ID', 'Title', 'Discount Code', 'Type', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = DiscountCode::select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\DiscountCodeController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\DiscountCodeController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->editColumn('type', '
                @if ($type == 1)
                    Free Use
                @elseif ($type == 2)
                    Cancellation
                @endif')
            ->escapeColumns([1, 2, 3])
            ->make();
    }

    public function editEntry($data, $id)
    {
        $fields = $data->except('_token');
        $fields['starts_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $fields['starts_at'], Auth::user()->timezone)->setTimezone('UTC');
        $fields['ends_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $fields['ends_at'], Auth::user()->timezone)->setTimezone('UTC');
        
        $this->model->where('id', $id)->update($fields);

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'num_codes');
        $fields['starts_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $fields['starts_at'], Auth::user()->timezone)->setTimezone('UTC');
        $fields['ends_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $fields['ends_at'], Auth::user()->timezone)->setTimezone('UTC');
        
        $num = $data->get('num_codes');

        for ($i = 1; $i <= $num; $i++) {
            if ($num > 1) {
                $fields['code'] = $this->generateCode();
            }
            $this->model->create($fields);
        }

        return true;
    }

    public function generateCode()
    {
        return StringHelper::generateDiscountCode();
    }

    public function export($data)
    {
        if ($data->has('start')) {
            $start = $data->get('start');
            $data = $this->model->where('created_at', '>=', $start . ' 00:00:00')->get();
        } else {
            $data = $this->model->get();
        }

        return $this->excel->create('discount-codes', function ($excel) use ($data) {
            $excel->sheet('Discount Codes', function ($sheet) use ($data) {
                $sheet->row(1, [
                    'ID', 'Title', 'Code', 'Type', 'Status'
                ]);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                });

                foreach ($data as $k => $v) {
                    $sheet->row(($k + 2), [
                        $v->id, $v->title, $v->code, ($v->type == 1 ? 'Free Use' : 'Cancellation'), ($v->status == 1 ? 'Active' : 'Inactive')
                    ]);
                }
            });
        })->download('xlsx');
    }
}
