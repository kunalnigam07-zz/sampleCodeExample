<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\InstructorListService;
use App\Http\Requests\Admin\InstructorListRequest;

class InstructorListController extends AdminController
{
	protected $permission = 'instructors/lists';
	protected $view_path = 'admin.instructors-lists.';
	protected $page;
	protected $service;

	public function __construct(InstructorListService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Instructors'], [$this->permission, 'Lists']];
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
    	$entry = $this->service->newEntry(['status' => 1]);
        $instructors_array = $this->service->instructorsArray();
        $list_members = [];
        $all_lists_array = [];

    	return view($this->view_path . 'manage', compact('page', 'entry', 'instructors_array', 'list_members'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $instructors_array = $this->service->instructorsArray();
        $list_members = $this->service->listMembers($id);
        $all_lists_array = $this->service->allLists();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'instructors_array', 'list_members', 'all_lists_array'));
    }

    public function create(InstructorListRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\InstructorListController@showIndex');
    }

    public function edit(InstructorListRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\InstructorListController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }

    public function action(Request $request, $id)
    {
        return $this->service->action($request, $id);
    }

    public function import(Request $request, $id)
    {
        return $this->service->import($request, $id);
    }
}
