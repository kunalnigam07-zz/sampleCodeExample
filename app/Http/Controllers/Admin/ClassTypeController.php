<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\ClassTypeService;
use App\Http\Requests\Admin\ClassTypeRequest;

class ClassTypeController extends AdminController
{
	protected $permission = 'classes/types';
	protected $view_path = 'admin.classes-types.';
	protected $page;
	protected $service;

	public function __construct(ClassTypeService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Classes'], [$this->permission, 'Class Types']];
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
    	$entry = $this->service->newEntry(['status' => 1, 'ordering' => $this->service->max('ordering') + 100]);
        $parents_array = $this->service->classTypeArray(2, true, false);

    	return view($this->view_path . 'manage', compact('page', 'entry', 'parents_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $parents_array = $this->service->classTypeArray(2, true, false);

    	return view($this->view_path . 'manage', compact('page', 'entry', 'parents_array'));
    }

    public function create(ClassTypeRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\ClassTypeController@showIndex');
    }

    public function edit(ClassTypeRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\ClassTypeController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
