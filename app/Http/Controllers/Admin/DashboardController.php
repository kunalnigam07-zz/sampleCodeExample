<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\DashboardService;

class DashboardController extends AdminController
{
	protected $page;
    protected $service;

	public function __construct(DashboardService $service)
    {
		$this->page = [];
        $this->service = $service;
	}

    public function index()
    {
        $page = $this->page;
        return view('admin.dashboard.index', compact('page'));
    }

    public function charts()
    {
        return $this->service->getCharts();
    }
}
