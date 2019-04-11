<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\ClassEventService;
use App\Http\Requests\Admin\ClassEventRequest;

class ClassEventController extends AdminController
{
	protected $permission = 'classes/classes';
	protected $view_path = 'admin.classes.';
	protected $page;
	protected $service;

	public function __construct(ClassEventService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Classes'], [$this->permission, 'Classes']];
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
    	$entry = $this->service->newEntry(['status' => 1, 'published' => 1, 'parent_id' => 0, 'level' => 1, 'privacy' => 1, 'bulk_allowed' => 1, 'has_music' => 1, 'tokbox_api_choice' => 1, 'flat_colour' => '000000', 'record_class' => 0]);
        $instructors_array = $this->service->instructorsArray();
        $types_array = $this->service->classTypeArray(3, false, true);

    	return view($this->view_path . 'manage', compact('page', 'entry', 'instructors_array', 'types_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $instructors_array = $this->service->instructorsArray();
        $types_array = $this->service->classTypeArray(3, false, true);

    	return view($this->view_path . 'manage', compact('page', 'entry', 'instructors_array', 'types_array'));
    }

    public function showExport()
    {
        $page = $this->page;
        array_push($page, ['', 'Export']);

        return view($this->view_path . 'export', compact('page'));
    }

    public function create(ClassEventRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\ClassEventController@showIndex');
    }

    public function edit(ClassEventRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\ClassEventController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }
}
