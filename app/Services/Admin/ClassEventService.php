<?php

namespace App\Services\Admin;

use Auth;
use Carbon;
use DateHelper;
use UploadHelper;
use App\Models\ClassEvent;
use Maatwebsite\Excel\Excel;
use App\Models\ClassEquipment;
use Yajra\Datatables\Datatables;

class ClassEventService extends AdminService
{
    protected $folder = 'classes';
    protected $columns = [
        'cols' => ['classes.id', 'classes.title', 'classes.price', 'classes.max_number', 'users.name AS iname', 'users.surname AS isurname', 'classes.class_at', 'classes.class_ends_at', 'classes.published', 'classes.status'],
        'exclude' => [],
        'order' => '0, "desc"'
    ];

    public function __construct(ClassEvent $model, ClassEquipment $classequipment, Excel $excel)
    {
        $this->model = $model;
        $this->classequipment = $classequipment;
        $this->excel = $excel;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\ClassEventController@create')], ['Export', 'file-excel-o', action('Admin\ClassEventController@showExport')]],
            'ajax' => route('classes.dt.list'),
            'labels' => ['ID', 'Class Name', 'Price', 'Bookings', 'First Name', 'Last Name', 'Class Date & Time', 'Ends At', 'Pub?', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

        return $table;
    }

    public function dtData()
    {
        $data = ClassEvent::select($this->columns['cols'])->join('users', 'classes.user_id', '=', 'users.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\ClassEventController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\ClassEventController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('class_at', '{{ DateHelper::showDate($class_at, \'d M Y - H:i\') }}')
            ->editColumn('class_ends_at', '{{ DateHelper::showDate($class_ends_at, \'H:i\') }}')
            ->editColumn('price', '{{ \'&pound;\' . $price }}')
            ->editColumn('max_number', function ($data) {
                return $data->totalBooked;
            })
            ->editColumn('published', '
                @if ($published == 1)
                    <i class="fa fa-check"></i>
                @else
                    <i class="fa fa-times"></i>
                @endif')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1, 2, 3])
            ->make();
    }

    public function editEntry($data, $id)
    {
        $fields = $data->except('_token', 'img', 'delete-img', 'type_array', 'equipment_title', 'equipment_ordering');
        $fields['class_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $fields['class_at'], Auth::user()->timezone)->setTimezone('UTC');
        $fields['class_ends_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $fields['class_ends_at'], Auth::user()->timezone)->setTimezone('UTC');

        if ($data->hasFile('img')) {
            $fields['img'] = UploadHelper::upload('img', $data, $this->folder, $id);
        }

        if ($data->has('delete-img')) {
            $fields['img'] = '';
        }

        $typeids = explode('_', $data->get('type_array'));
        $fields['type_1_id'] = $typeids[0];
        $fields['type_2_id'] = $typeids[1];
        $fields['type_3_id'] = $typeids[2];

        $this->model->where('id', $id)->update($fields);

        $this->updateEquipment($id, $data);

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'img', 'delete-img', 'type_array', 'equipment_title', 'equipment_ordering', '_dur');
        $fields['class_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $fields['class_at'], Auth::user()->timezone)->setTimezone('UTC');
        $fields['class_ends_at'] = Carbon::createFromFormat('Y-m-d H:i:s', DateHelper::addMinutes($data->get('dur_'), $fields['class_at']), Auth::user()->timezone)->setTimezone('UTC');

        $typeids = explode('_', $data->get('type_array'));
        $fields['type_1_id'] = $typeids[0];
        $fields['type_2_id'] = $typeids[1];
        $fields['type_3_id'] = $typeids[2];

        $new = $this->model->create($fields);

        if ($data->hasFile('img')) {
            $new->img = UploadHelper::upload('img', $data, $this->folder, $new->id);
            $new->save();
        }

        if ($data->has('delete-img')) {
            $new->img = '';
            $new->save();
        }

        $this->updateEquipment($new->id, $data);

        return true;
    }

    public function deleteEntry($id)
    {
        UploadHelper::deleteDirectory($this->folder, $id);

        return $this->model->destroy($id);
    }

    public function updateEquipment($id, $request)
    {
        $this->classequipment->where('class_id', $id)->delete();

        $eqs = $request->get('equipment_title');
        $orderings = $request->get('equipment_ordering');

        foreach ($eqs as $k => $v) {
            if (strlen($v) > 0) {
                $this->classequipment->create(['title' => $v, 'ordering' => $orderings[$k], 'class_id' => $id, 'status' => 1]);
            }
        }
    }

    public function export($data)
    {
        if ($data->has('start')) {
            $start = $data->get('start');
        } else {
            $start = '2016-01-01';
        }

        $data = $this->model->select('classes.id', 'classes.title', 'classes_types.title AS cttitle', 'instructors.name AS iname', 'instructors.surname AS isurname', 'classes.price', 'classes.class_at', 'classes.class_ends_at', 'classes.actual_start_at', 'classes.actual_end_at', 'classes.cancelled_at', 'classes.about', 'classes.max_number', 'classes.level', 'classes.privacy', 'classes.bulk_allowed', 'classes.parent_id', 'classes.status')
            ->where('classes.created_at', '>=', $start . ' 00:00:00')
            ->join('classes_types', 'classes.type_1_id', '=', 'classes_types.id')
            ->join('users AS instructors', 'classes.user_id', '=', 'instructors.id')
            ->get();

        return $this->excel->create('classes', function ($excel) use ($data) {
            $excel->sheet('Classes', function ($sheet) use ($data) {
                $sheet->row(1, [
                    'ID', 'Class Name', 'Primary Type', 'Instructor', 'Price', 'Start', 'End', 'Actual Start', 'Actual End', 'Cancellation', 'About', 'Max Members', 'Level', 'Privacy', 'Bulk Allowed', 'Equipment', 'Is Series', 'Status'
                ]);
                $sheet->row(1, function($row) {
                    $row->setFontWeight('bold');
                });

                foreach ($data as $k => $v) {
                    $equipment = $v->equipment()->lists('title')->all();
                    $sheet->row(($k + 2), [
                        $v->id, $v->title, $v->cttitle, $v->iname . ' ' . $v->isurname, $v->price, DateHelper::showDate($v->class_at), DateHelper::showDate($v->class_ends_at), ($v->actual_start_at != null ? DateHelper::showDate($v->actual_start_at) : ''), ($v->actual_end_at != null ? DateHelper::showDate($v->actual_end_at) : ''), ($v->cancelled_at != null ? DateHelper::showDate($v->cancelled_at) : ''), $v->about, $v->max_number, str_replace([1, 2, 3], ['Beginner', 'Intermediate', 'Advanced'], $v->level), ($v->privacy == 1 ? 'Public' : 'Private'), ($v->bulk_allowed == 1 ? 'Yes' : 'No'), implode(', ', $equipment), ($v->parent_id == 0 ? 'No' : 'Yes'), ($v->status == 1 ? 'Active' : 'Inactive')
                    ]);
                }
            });
        })->download('xlsx');
    }
}
