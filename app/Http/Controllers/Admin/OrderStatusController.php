<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\OrderStatusService;
use App\Http\Requests\Admin\OrderStatusRequest;

class OrderStatusController extends AdminController
{
	protected $permission = 'orders/statuses';
	protected $view_path = 'admin.orders-statuses.';
	protected $page;
	protected $service;

	public function __construct(OrderStatusService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Orders'], [$this->permission, 'Statuses']];
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

    public function create(OrderStatusRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\OrderStatusController@showIndex');
    }

    public function edit(OrderStatusRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\OrderStatusController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
