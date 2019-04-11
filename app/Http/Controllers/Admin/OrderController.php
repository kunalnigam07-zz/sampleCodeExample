<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\OrderService;
use App\Http\Requests\Admin\OrderRequest;

class OrderController extends AdminController
{
	protected $permission = 'orders/orders';
	protected $view_path = 'admin.orders.';
	protected $page;
	protected $service;

	public function __construct(OrderService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Payments'], [$this->permission, 'Payments']];
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

    public function edit(OrderRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\OrderController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }
}
