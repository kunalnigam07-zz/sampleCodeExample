<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\BanService;
use App\Http\Requests\Admin\BanRequest;

class BanController extends AdminController
{
	protected $permission = 'moderation/bans';
	protected $view_path = 'admin.bans.';
	protected $page;
	protected $service;

	public function __construct(BanService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Moderation'], [$this->permission, 'Bans']];
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

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function create(BanRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\BanController@showIndex');
    }

    public function edit(BanRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\BanController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
