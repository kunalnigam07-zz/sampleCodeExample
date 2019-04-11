<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use App\Services\Admin\SettingService;
use App\Services\Amazon\InstanceIdGuesser;
use Lang;

class SettingController extends AdminController
{
	protected $permission = 'settings/general';
	protected $view_path = 'admin.settings-general.';
	protected $page;
	protected $service;

	/* @var InstanceIdGuesser */
    protected $instanceIdGuesser;

    public function __construct(SettingService $service, InstanceIdGuesser $instanceIdGuesser)
    {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Settings'], [$this->permission, 'General Settings']];
        $this->instanceIdGuesser = $instanceIdGuesser;
    }

    public function showEdit()
    {
    	$page = $this->page;
    	array_push($page, ['', 'Edit']);
    	$entry = $this->service->getEntry(1);

    	return view($this->view_path . 'manage', compact('page', 'entry'));
    }

    public function edit(SettingRequest $request)
    {
        $wasAwsInstanceIdUpdated = $this->processUpdatingAwsInstanceId($this->service->getEntry(1), $request);

        $this->service->editEntry($request, 1);

        $res = redirect()
            ->action('Admin\SettingController@showEdit')
            ->with('flash_message_success', 'Your changes have been saved.');

        if ($wasAwsInstanceIdUpdated === false) {
            $res->with('flash_message_error', Lang::get('messages.aws_wasnot_updated_automatically'));
        }

        return $res;
    }

    protected function processUpdatingAwsInstanceId(Setting $current, SettingRequest $request): ?bool
    {
        $lsUrl = $request->get('liveswitch_url');
        $wasLsUrlChanged = $current->liveswitch_url != $lsUrl;

        if (!$wasLsUrlChanged && !empty($current->aws_instance_id)) {
            return null;
        }

        $idByUrl = $id = $this->instanceIdGuesser->byUrl($lsUrl);

        if ($idByUrl === null && ($wasLsUrlChanged || empty($request->get('aws_instance_id')))) {
            $request->request->set('aws_instance_id', '');
            return false;
        }

        if ($idByUrl !== null) {
            $request->request->set('aws_instance_id', $id);
        }

        return true;
    }
}
