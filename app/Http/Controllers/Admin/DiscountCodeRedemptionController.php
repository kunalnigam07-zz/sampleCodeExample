<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\DiscountCodeRedemptionService;
use App\Http\Requests\Admin\DiscountCodeRedemptionRequest;

class DiscountCodeRedemptionController extends AdminController
{
	protected $permission = 'orders/discount-code-redemptions';
	protected $view_path = 'admin.discount-codes-redemptions.';
	protected $page;
	protected $service;

	public function __construct(DiscountCodeRedemptionService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Orders'], [$this->permission, 'Discount Code Redemptions']];
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
    	array_push($page, ['', 'View']);
    	$entry = $this->service->getEntry($id);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function edit(DiscountCodeRedemptionRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\DiscountCodeRedemptionController@showIndex');
    }
}
