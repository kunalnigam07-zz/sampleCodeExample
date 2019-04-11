<?php

namespace App\Services\Admin;

use Carbon;
use UploadHelper;
use App\Models\Slider;
use Yajra\Datatables\Datatables;

class SliderService extends AdminService
{
    protected $folder = 'sliders';
    protected $columns = [
        'cols' => ['id', 'title', 'ordering', 'updated_at', 'status'],
        'exclude' => [],
        'order' => '2, "asc"'
    ];

    public function __construct(Slider $model)
    {
        $this->model = $model;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\SliderController@create')]],
            'ajax' => route('sliders.dt.list'),
            'labels' => ['ID', 'Title', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = Slider::select($this->columns['cols']);

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\SliderController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\SliderController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1])
            ->make();
    }

    public function editEntry($data, $id)
    {
        $fields = $data->except('_token', 'img_left', 'delete-img_left', 'img_right', 'delete-img_right');
      
        if ($data->hasFile('img_left')) {
            $fields['img_left'] = UploadHelper::upload('img_left', $data, $this->folder, $id);
        }

        if ($data->hasFile('img_right')) {
            $fields['img_right'] = UploadHelper::upload('img_right', $data, $this->folder, $id);
        }

        if ($data->has('delete-img_left')) {
            $fields['img_left'] = '';
        }

        if ($data->has('delete-img_right')) {
            $fields['img_right'] = '';
        }
        
        $this->model->where('id', $id)->update($fields);

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'img_left', 'delete-img_left', 'img_right', 'delete-img_right');
        
        $new = $this->model->create($fields);

        if ($data->hasFile('img_left')) {
            $new->img_left = UploadHelper::upload('img_left', $data, $this->folder, $new->id);
            $new->save();
        }

        if ($data->hasFile('img_right')) {
            $new->img_right = UploadHelper::upload('img_right', $data, $this->folder, $new->id);
            $new->save();
        }

        if ($data->has('delete-img_left')) {
            $new->img_left = '';
            $new->save();
        }

        if ($data->has('delete-img_right')) {
            $new->img_right = '';
            $new->save();
        }

        return true;
    }

    public function deleteEntry($id)
    {
        UploadHelper::deleteDirectory($this->folder, $id);

        return $this->model->destroy($id);
    }
}
