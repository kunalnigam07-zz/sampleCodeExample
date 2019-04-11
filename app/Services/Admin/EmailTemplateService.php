<?php

namespace App\Services\Admin;

use App\Models\EmailTemplate;
use Yajra\Datatables\Datatables;

class EmailTemplateService extends AdminService
{
    protected $columns = [
        'cols' => ['id', 'title', 'subject', 'updated_at'],
        'exclude' => [],
        'order' => '0, "asc"'
    ];

    public function __construct(EmailTemplate $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [],
            'ajax' => route('emails.dt.list'),
            'labels' => ['ID', 'Email Title', 'Email Subject', 'Updated', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = $this->model->select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\EmailTemplateController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->escapeColumns([1])
            ->make();
    }
}
