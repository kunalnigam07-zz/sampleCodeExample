<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\GridService;
use App\Http\Requests\Admin\GridRequest;

class GridController extends AdminController
{
	protected $permission = 'content/grid';
	protected $view_path = 'admin.grid.';
	protected $page;
	protected $service;

	public function __construct(GridService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Content'], [$this->permission, 'Grid']];
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
        $sliders_array = $this->service->slidersArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'sliders_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $sliders_array = $this->service->slidersArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'sliders_array'));
    }

    public function create(GridRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\GridController@showIndex');
    }

    public function edit(GridRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\GridController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
