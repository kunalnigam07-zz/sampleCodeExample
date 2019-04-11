<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\PageService;
use App\Http\Requests\Admin\PageRequest;

class PageController extends AdminController
{
	protected $permission = 'content/pages';
	protected $view_path = 'admin.pages.';
	protected $page;
	protected $service;

	public function __construct(PageService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Content'], [$this->permission, 'Pages']];
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
        $entry = $this->service->newEntry(['status' => 1, 'user_added' => 1, 'ordering' => $this->service->max('ordering') + 100, 'url' => '/']);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function create(PageRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\PageController@showIndex');
    }

    public function edit(PageRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\PageController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
