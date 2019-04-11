<?php

namespace App\Services\Admin;

use Carbon;
use UploadHelper;
use App\Models\Grid;
use App\Models\Slider;
use Yajra\Datatables\Datatables;

class GridService extends AdminService
{
    protected $folder = 'grid';
    protected $columns = [
        'cols' => ['grid.id', 'grid.title', 'sliders.title AS stitle', 'grid.ordering', 'grid.updated_at', 'grid.status'],
        'exclude' => [],
        'order' => '3, "asc"'
    ];

    public function __construct(Grid $model, Slider $slider)
    {
        $this->model = $model;
        $this->slider = $slider;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [],
            'ajax' => route('grid.dt.list'),
            'labels' => ['ID', 'Title', 'Slider', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = Grid::select($this->columns['cols'])->join('sliders', 'grid.slider_id', '=', 'sliders.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\GridController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <!--<a href="{{ action(\'Admin\GridController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>-->')
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
        $fields = $data->except('_token', 'img', 'delete-img');
      
        if ($data->hasFile('img')) {
            $fields['img'] = UploadHelper::upload('img', $data, $this->folder, $id);
        }

        if ($data->has('delete-img')) {
            $fields['img'] = '';
        }
        
        $this->model->where('id', $id)->update($fields);

        return true;
    }

    public function createEntry($data)
    {
        $fields = $data->except('_token', 'img', 'delete-img');
        
        $new = $this->model->create($fields);

        if ($data->hasFile('img')) {
            $new->img = UploadHelper::upload('img', $data, $this->folder, $new->id);
            $new->save();
        }

        if ($data->has('delete-img')) {
            $new->img = '';
            $new->save();
        }

        return true;
    }

    public function deleteEntry($id)
    {
        UploadHelper::deleteDirectory($this->folder, $id);

        return $this->model->destroy($id);
    }

    public function slidersArray()
    {
        $data = [];
        $sliders = $this->slider->select('id', 'title')->orderBy('title', 'ASC')->get();

        foreach ($sliders as $slider) {
            $data[$slider->id] = $slider->title;
        }

        return $data;
    }
}
