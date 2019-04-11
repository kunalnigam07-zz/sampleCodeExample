<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\MemberService;
use App\Http\Requests\Admin\MemberRequest;

class MemberController extends AdminController
{
	protected $permission = 'members/members';
	protected $view_path = 'admin.members.';
	protected $page;
	protected $service;

	public function __construct(MemberService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Members'], [$this->permission, 'Members']];
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
    	$entry = $this->service->newEntry(['status' => 1, 'timezone' => 'Europe/London', 'country_id' => 218, 'pre_authenticated' => 1]);
        $timezones_array = $this->service->timezonesArray();
        $countries_array = $this->service->countriesArray();
        $countries_codes_array = $this->service->countriesArray(true);

    	return view($this->view_path . 'manage', compact('page', 'entry', 'timezones_array', 'countries_array', 'countries_codes_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $timezones_array = $this->service->timezonesArray();
        $countries_array = $this->service->countriesArray();
        $countries_codes_array = $this->service->countriesArray(true);

    	return view($this->view_path . 'manage', compact('page', 'entry', 'timezones_array', 'countries_array', 'countries_codes_array'));
    }

    public function showExport()
    {
        $page = $this->page;
        array_push($page, ['', 'Export']);

        return view($this->view_path . 'export', compact('page'));
    }

    public function showImport()
    {
        $page = $this->page;
        array_push($page, ['', 'Import']);
        $timezones_array = $this->service->timezonesArray();
        $countries_array = $this->service->countriesArray();

        return view($this->view_path . 'import', compact('page', 'timezones_array', 'countries_array'));
    }

    public function showImportResult($id)
    {
        $page = $this->page;
        array_push($page, ['', 'Import Result']);
        $results = $this->service->parseMemberCSV($id);

        return view($this->view_path . 'import-result', compact('page', 'results'));
    }

    public function create(MemberRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\MemberController@showIndex');
    }

    public function edit(MemberRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\MemberController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    public function import(Request $request)
    {
        return $this->service->import($request);
    }

    public function importFinalise(Request $request)
    {
        return $this->service->importFinalise($request);
    }

    public function loginAs($id)
    {
        return $this->service->loginAs($id);
    }
}
