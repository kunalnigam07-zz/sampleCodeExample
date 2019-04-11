<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\InstructorService;
use App\Http\Requests\Admin\InstructorRequest;

class InstructorController extends AdminController
{
	protected $permission = 'instructors/instructors';
	protected $view_path = 'admin.instructors.';
	protected $page;
	protected $service;

	public function __construct(InstructorService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Instructors'], [$this->permission, 'Instructors']];
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
    	$entry = $this->service->newEntry(['status' => 1, 'timezone' => 'Europe/London', 'country_id' => 218, 'pre_authenticated' => 1, 'years_experience' => 0]);
        $timezones_array = $this->service->timezonesArray();
        $experience_array = $this->service->experienceArray();
        $countries_array = $this->service->countriesArray();
        $countries_codes_array = $this->service->countriesArray(true);

    	return view($this->view_path . 'manage', compact('page', 'entry', 'timezones_array', 'experience_array', 'countries_array', 'countries_codes_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $timezones_array = $this->service->timezonesArray();
        $experience_array = $this->service->experienceArray();
        $countries_array = $this->service->countriesArray();
        $countries_codes_array = $this->service->countriesArray(true);

    	return view($this->view_path . 'manage', compact('page', 'entry', 'timezones_array', 'experience_array', 'countries_array', 'countries_codes_array'));
    }

    public function showExport()
    {
        $page = $this->page;
        array_push($page, ['', 'Export']);

        return view($this->view_path . 'export', compact('page'));
    }

    public function showEarnings()
    {
        $page = $this->page;
        array_push($page, ['', 'Earnings']);

        return view($this->view_path . 'earnings', compact('page'));
    }

    public function create(InstructorRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\InstructorController@showIndex');
    }

    public function edit(InstructorRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\InstructorController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    public function earnings(Request $request)
    {
        return $this->service->earnings($request);
    }

    public function loginAs($id)
    {
        return $this->service->loginAs($id);
    }
}
