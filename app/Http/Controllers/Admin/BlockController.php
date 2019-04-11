<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\BlockService;
use App\Http\Requests\Admin\BlockRequest;

class BlockController extends AdminController
{
	protected $permission = 'content/blocks';
	protected $view_path = 'admin.blocks.';
	protected $page;
	protected $service;

	public function __construct(BlockService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Content'], [$this->permission, 'Text Blocks']];
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

    public function edit(BlockRequest $request, $id)
    {
        $this->service->editEntry($request, $id);

        return redirect()->action('Admin\BlockController@showIndex', ['start' => $request->get('page_start_pp')]);
    }
}
