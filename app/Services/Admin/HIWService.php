<?php

namespace App\Services\Admin;

use Carbon;
use UploadHelper;
use App\Models\HIW;
use App\Models\HIWCategory;
use Yajra\Datatables\Datatables;

class HIWService extends AdminService
{
    protected $folder = 'hiw';
    protected $columns = [
        'cols' => ['hiw.id', 'hiw.contents', 'hiw_categories.title', 'hiw_categories.section_id', 'hiw.ordering', 'hiw.updated_at', 'hiw.status'],
        'exclude' => [],
        'order' => '4, "asc"'
    ];

    public function __construct(HIW $model, HIWCategory $hiwcategory)
    {
        $this->model = $model;
        $this->hiwcategory = $hiwcategory;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [],
            'ajax' => route('hiw.dt.list'),
            'labels' => ['ID', 'Text', 'Category', 'Section', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = HIW::select($this->columns['cols'])->join('hiw_categories', 'hiw.category_id', '=', 'hiw_categories.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\HIWController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a><!--
                <a href="{{ action(\'Admin\HIWController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>-->')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->editColumn('contents', '{{ StringHelper::chop(strip_tags($contents), 100) }}')
            ->editColumn('section_id', '{{ str_replace([1, 2], [\'For Members\', \'For Instructors\'], $section_id) }}')
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

    public function categoriesArray()
    {
        $data = [];
        $categories = $this->hiwcategory->select('id', 'title', 'section_id')->orderBy('ordering', 'ASC')->get();

        foreach ($categories as $category) {
            $data[$category->id] = $category->title . ' (' . ($category->section_id == 1 ? 'Members' : 'Instructors') . ')';
        }

        return $data;
    }
}
