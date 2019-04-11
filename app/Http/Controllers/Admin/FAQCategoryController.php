<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\FAQCategoryService;
use App\Http\Requests\Admin\FAQCategoryRequest;

class FAQCategoryController extends AdminController
{
	protected $permission = 'faqs/faqs-categories';
	protected $view_path = 'admin.faqs-categories.';
	protected $page;
	protected $service;

	public function __construct(FAQCategoryService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'FAQs'], [$this->permission, 'FAQ Categories']];
	}

	public function dtlist()
    {
    	return $this->service->dtData();
    }

    public function showIndex()
    {
        $page = $this->page;
        $dt = $this->service->dtTable();

        return view($this->view_path . 'index', compact('page', 'dt'));
    }

    public function showCreate()
    {
    	$page = $this->page;
    	array_push($page, ['', 'Create']);
    	$entry = $this->service->newEntry(['status' => 1, 'ordering' => $this->service->max('ordering') + 100, 'section_id' => 1]);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function create(FAQCategoryRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\FAQCategoryController@showIndex');
    }

    public function edit(FAQCategoryRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\FAQCategoryController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
