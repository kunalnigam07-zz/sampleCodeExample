<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\InstructorVideoService;
use App\Http\Requests\Admin\InstructorVideoRequest;

class InstructorVideoController extends AdminController
{
	protected $permission = 'instructors/videos';
	protected $view_path = 'admin.instructors-videos.';
	protected $page;
	protected $service;

	public function __construct(InstructorVideoService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Instructors'], [$this->permission, 'Videos']];
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
        $instructors_array = $this->service->instructorsArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'instructors_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $instructors_array = $this->service->instructorsArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'instructors_array'));
    }

    public function create(InstructorVideoRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\InstructorVideoController@showIndex');
    }

    public function edit(InstructorVideoRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\InstructorVideoController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
