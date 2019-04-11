<?php

namespace App\Services\Admin;

use Carbon;
use DateHelper;
use App\Models\Booking;
use App\Models\ClassEvent;
use Maatwebsite\Excel\Excel;
use Yajra\Datatables\Datatables;

class BookingService extends AdminService
{
    protected $columns = [
        'cols' => ['bookings.id', 'classes.title', 'classes.class_at', 'users.name', 'users.surname', 'bookings.created_at', 'bookings.status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(Booking $model, ClassEvent $classevent, Excel $excel)
    {
        $this->model = $model;
        $this->classevent = $classevent;
        $this->excel = $excel;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\BookingController@create')], ['Export', 'file-excel-o', action('Admin\BookingController@showExport')]],
            'ajax' => route('bookings.dt.list'),
            'labels' => ['ID', 'Class Name', 'Class Date & Time', 'First Name', 'Last Name', 'Booking Made', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = Booking::select($this->columns['cols'])->join('classes', 'bookings.class_id', '=', 'classes.id')->join('users', 'bookings.user_id', '=', 'users.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\BookingController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\BookingController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('class_at', '{{ DateHelper::showDate($class_at, \'d M Y - H:i\') }}')
            ->editColumn('created_at', '{{ DateHelper::showDate($created_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1, 2, 3, 4])
            ->make();
    }

    public function classesArray()
    {
        $ret = [];
        $classes = $this->classevent->select('classes.id', 'classes.title', 'classes.class_at', 'users.name AS iname', 'users.surname AS isurname')
            ->join('users', 'classes.user_id', '=', 'users.id')
            ->where('classes.class_at', '>=', Carbon::now()->format('Y-m-d') . ' 00:00:00')
            ->orderBy('class_at', 'DESC')
            ->get();

        foreach ($classes as $class) {
            $ret[$class->id] = DateHelper::showDate($class->class_at, 'd M \a\t H:i') . ' - ' . $class->title . ' (' . $class->iname . ' ' . $class->isurname . ') - ID #' . $class->id;
        }

        return $ret;
    }

    public function export($data)
    {
        if ($data->has('start')) {
            $start = $data->get('start');
        } else {
            $start = '2016-01-01';
        }

        $data = $this->model->select('bookings.id', 'bookings.rating', 'bookings.comments', 'users.name AS mname', 'users.surname AS msurname', 'classes.title AS ctitle', 'classes.class_at', 'bookings.updated_at', 'bookings.joined_at', 'bookings.cancelled_at', 'bookings.status', 'instructors.name AS iname', 'instructors.surname AS isurname')
            ->where('bookings.created_at', '>=', $start . ' 00:00:00')
            ->join('classes', 'bookings.class_id', '=', 'classes.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('users AS instructors', 'classes.user_id', '=', 'instructors.id')
            ->get();

        return $this->excel->create('bookings', function ($excel) use ($data) {
            $excel->sheet('Bookings', function ($sheet) use ($data) {
                $sheet->row(1, [
                    'ID', 'Member', 'Class Name', 'Instructor', 'Class Date', 'Rating', 'Comments', 'Joined At', 'Cancellation', 'Status'
                ]);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                });

                foreach ($data as $k => $v) {
                    $sheet->row(($k + 2), [
                        $v->id, $v->mname . ' ' . $v->msurname, $v->ctitle, $v->iname . ' ' . $v->isurname, DateHelper::showDate($v->class_at), $v->rating, $v->comments, ($v->joined_at != null ? DateHelper::showDate($v->joined_at) : ''), ($v->cancelled_at != null ? DateHelper::showDate($v->cancelled_at) : ''), ($v->status == 1 ? 'Active' : 'Inactive')
                    ]);
                }
            });
        })->download('xlsx');
    }
}
