<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\UserService;
use App\Http\Requests\Admin\UserRequest;

class UserController extends AdminController
{
	protected $permission = 'settings/users';
	protected $view_path = 'admin.users.';
	protected $page;
	protected $service;

	public function __construct(UserService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Settings'], [$this->permission, 'Admin Users']];
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
    	$entry = $this->service->newEntry(['status' => 1, 'admin_type' => 2, 'is_paid' => 0, 'display_earnings' => 1, 'notify_forum' => 1, 'notify_daily_offers' => 1, 'acorns' => 0, 'show_dashboard' => 1]);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function create(UserRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\UserController@showIndex');
    }

    public function edit(UserRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\UserController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
