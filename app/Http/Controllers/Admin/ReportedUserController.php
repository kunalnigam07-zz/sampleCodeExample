<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\ReportedUserService;
use App\Http\Requests\Admin\ReportedUserRequest;

class ReportedUserController extends AdminController
{
	protected $permission = 'moderation/reported-users';
	protected $view_path = 'admin.reported-users.';
	protected $page;
	protected $service;

	public function __construct(ReportedUserService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Moderation'], [$this->permission, 'Reported Users']];
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

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function edit(ReportedUserRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\ReportedUserController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
