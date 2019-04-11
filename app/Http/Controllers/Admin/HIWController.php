<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\HIWService;
use App\Http\Requests\Admin\HIWRequest;

class HIWController extends AdminController
{
	protected $permission = 'content/hiw';
	protected $view_path = 'admin.hiw.';
	protected $page;
	protected $service;

	public function __construct(HIWService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Content'], [$this->permission, 'How it Works']];
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
        $categories_array = $this->service->categoriesArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'categories_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $categories_array = $this->service->categoriesArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'categories_array'));
    }

    public function create(HIWRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\HIWController@showIndex');
    }

    public function edit(HIWRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\HIWController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
