<?php

namespace App\Services\Admin;

use App\Models\ReportedUser;
use Yajra\Datatables\Datatables;

class ReportedUserService extends AdminService
{
    protected $columns = [
        'cols' => ['reported_users.id', 'user1.name AS u1n', 'user1.surname AS u1s', 'user2.name AS u2n', 'user2.surname AS u2s', 'reported_users.updated_at', 'reported_users.status'],
        'exclude' => [],
        'order' => '2, "asc"'
    ];

    public function __construct(ReportedUser $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [],
            'ajax' => route('reported-users.dt.list'),
            'labels' => ['ID', 'First Name', 'Last Name', 'Offender First Name', 'Offender Last Name', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = ReportedUser::select($this->columns['cols'])
            ->join('users AS user1', 'reported_users.user_id', '=', 'user1.id')
            ->join('users AS user2', 'reported_users.reported_user_id', '=', 'user2.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\ReportedUserController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\ReportedUserController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Resolved</span>
                @else
                    <span class="group red">Unresolved</span>
                @endif')
            ->escapeColumns([1, 2, 3, 4])
            ->make();
    }
}
