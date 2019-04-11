<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\CountryService;
use App\Http\Requests\Admin\CountryRequest;

class CountryController extends AdminController
{
	protected $permission = 'settings/countries';
	protected $view_path = 'admin.countries.';
	protected $page;
	protected $service;

	public function __construct(CountryService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Settings'], [$this->permission, 'Countries']];
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
    	$entry = $this->service->newEntry(['status' => 1, 'sanctioned' => 0]);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function create(CountryRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\CountryController@showIndex');
    }

    public function edit(CountryRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\CountryController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
