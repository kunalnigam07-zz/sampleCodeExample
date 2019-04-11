<?php

namespace App\Services\Admin;

use App\Models\Block;
use Yajra\Datatables\Datatables;

class BlockService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'title', 'contents', 'updated_at', 'block_type'],
        'exclude' => ['block_type'],
        'order' => '0, "asc"'
    ];

    public function __construct(Block $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [],
            'ajax' => route('blocks.dt.list'),
            'labels' => ['ID', 'Title', 'Contents', 'Updated', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = $this->model->select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\BlockController@edit\', $id) }}?start=' . \Request::get('start') . '" class="edit"><i class="fa fa-pencil"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('contents','
                @if ($block_type == \'HTML\')
                    <em>(HTML Value Not Displayed)</em>
                @else
                    {{ $contents }}
                @endif')
            ->removeColumn('block_type')
            ->escapeColumns([1])
            ->make();
    }
}
