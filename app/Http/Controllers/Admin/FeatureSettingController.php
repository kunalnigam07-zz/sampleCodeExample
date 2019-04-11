<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\FeatureSettingService;
use App\Http\Requests\Admin\FeatureSettingRequest;

class FeatureSettingController extends AdminController
{
	protected $permission = 'settings/features';
	protected $view_path = 'admin.settings-features.';
	protected $page;
	protected $service;

	public function __construct(FeatureSettingService $service)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Settings'], [$this->permission, 'Feature Settings']];
	}

    public function showEdit()
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry(1);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function edit(FeatureSettingRequest $request)
    {
        $this->service->editEntry($request, 1);

        return redirect()->action('Admin\FeatureSettingController@showEdit')->with('flash_message_success', 'Your changes have been saved.');
    }
}
