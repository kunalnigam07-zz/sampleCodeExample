<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\BookingService;
use App\Http\Requests\Admin\BookingRequest;

class BookingController extends AdminController
{
	protected $permission = 'classes/bookings';
	protected $view_path = 'admin.bookings.';
	protected $page;
	protected $service;

	public function __construct(BookingService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Classes'], [$this->permission, 'Bookings']];
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
        $classes_array = $this->service->classesArray();
        $users_array = $this->service->membersArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'classes_array', 'users_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $classes_array = $this->service->classesArray();
        $users_array = $this->service->membersArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'classes_array', 'users_array'));
    }

    public function showExport()
    {
        $page = $this->page;
        array_push($page, ['', 'Export']);

        return view($this->view_path . 'export', compact('page'));
    }

    public function create(BookingRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\BookingController@showIndex');
    }

    public function edit(BookingRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\BookingController@showIndex');
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
