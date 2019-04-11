<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\EmailTemplateService;
use App\Http\Requests\Admin\EmailTemplateRequest;

class EmailTemplateController extends AdminController
{
	protected $permission = 'settings/emails';
	protected $view_path = 'admin.emails.';
	protected $page;
	protected $service;

	public function __construct(EmailTemplateService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'System'], [$this->permission, 'Email Templates']];
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

    public function create(EmailTemplateRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\EmailTemplateController@showIndex');
    }

    public function edit(EmailTemplateRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\EmailTemplateController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
