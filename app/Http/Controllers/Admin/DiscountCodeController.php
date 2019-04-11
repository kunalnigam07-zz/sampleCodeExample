<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\DiscountCodeService;
use App\Http\Requests\Admin\DiscountCodeRequest;

class DiscountCodeController extends AdminController
{
	protected $permission = 'orders/discount-codes';
	protected $view_path = 'admin.discount-codes.';
	protected $page;
	protected $service;

	public function __construct(DiscountCodeService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Orders'], [$this->permission, 'Discount Codes']];
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
    	$entry = $this->service->newEntry(['status' => 1, 'type' => 1, 'instructor_id' => 0, 'code' => $this->service->generateCode(), 'class_max_number' => 0]);
        $instructors_array = $this->service->instructorsArray();
        $members_array = $this->service->membersArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'instructors_array', 'members_array'));
    }

    public function showEdit($id)
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry($id);
        $instructors_array = $this->service->instructorsArray();
        $members_array = $this->service->membersArray();

    	return view($this->view_path . 'manage', compact('page', 'entry', 'instructors_array', 'members_array'));
    }

    public function showExport()
    {
        $page = $this->page;
        array_push($page, ['', 'Export']);

        return view($this->view_path . 'export', compact('page'));
    }

    public function create(DiscountCodeRequest $request)
    {
    	$this->service->createEntry($request);

        return redirect()->action('Admin\DiscountCodeController@showIndex');
    }

    public function edit(DiscountCodeRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\DiscountCodeController@showIndex');
    }

    public function delete($id)
    {
    	$entry = $this->service->deleteEntry($id);
    }

    public function export(Request $request)
    {
        return $this->service->export($request);
    }
}
