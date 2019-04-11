<?php

namespace App\Services\Admin;

use DB;
use App\Models\FAQ;
use App\Models\FAQCategory;
use Yajra\Datatables\Datatables;

class FAQService extends AdminService
{
    protected $columns = [
        'cols' => ['faqs.id', 'faqs.question', 'faqs_categories.title AS ctitle', 'faqs_categories.section_id AS csection', 'faqs.ordering', 'faqs.updated_at', 'faqs.status'],
        'exclude' => [],
        'order' => '4, "asc"'
    ];

    public function __construct(FAQ $model, FAQCategory $faqcategory)
    {
        $this->model = $model;
        $this->faqcategory = $faqcategory;
    }

    public function dtTable()
    {
        $table = [
            'buttons' => [['Create New', 'plus', action('Admin\FAQController@create')]],
            'ajax' => route('faqs.dt.list'),
            'labels' => ['ID', 'Question', 'Category', 'Section', 'Ordering', 'Updated', 'Status', 'Actions'],
            'columns' => $this->columns
        ];

    	return $table;
    }

    public function dtData()
    {
        $data = $this->model->select($this->columns['cols'])->join('faqs_categories', 'faqs.category_id', '=', 'faqs_categories.id');

        return Datatables::of($data)
            ->addColumn('operations', '<a href="{{ action(\'Admin\FAQController@edit\', $id) }}" class="edit"><i class="fa fa-pencil"></i></a>
                <a href="{{ action(\'Admin\FAQController@delete\', $id) }}" class="delete"><i class="fa fa-times"></i></a>')
            ->editColumn('updated_at', '{{ DateHelper::showDate($updated_at) }}')
            ->editColumn('csection', '{{ $csection == 1 ? \'Website\' : \'Instructors\' }}')
            ->editColumn('status', '
                @if ($status)
                    <span class="group green">Active</span>
                @else
                    <span class="group red">Inactive</span>
                @endif')
            ->escapeColumns([1, 2, 3])
            ->make();
    }

    public function categoriesArray()
    {
        $data = [];
        $categories = $this->faqcategory->select('id', 'title', 'section_id')->orderBy('title', 'ASC')->get();

        foreach ($categories as $category) {
            $data[$category->id] = $category->title . ' (' . ($category->section_id == 1 ? 'Website' : 'Instructors') . ')';
        }

        return $data;
    }
}
