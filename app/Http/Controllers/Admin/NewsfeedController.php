<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\NewsfeedService;
use App\Http\Requests\Admin\NewsfeedRequest;

class NewsfeedController extends AdminController
{
	protected $permission = 'content/newsfeed';
	protected $view_path = 'admin.newsfeed.';
	protected $page;
	protected $service;

	public function __construct(NewsfeedService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Content'], [$this->permission, 'Newsfeed / Notifications']];
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
    	$entry = $this->service->newEntry(['status' => 1, 'section_id' => 1, 'for_users' => 0, 'ordering' => $this->service->max('ordering') + 100]);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function create(NewsfeedRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\NewsfeedController@showIndex');
    }

    public function edit(NewsfeedRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\NewsfeedController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
