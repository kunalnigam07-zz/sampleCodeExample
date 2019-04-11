<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\LiveswitchSettingRequest;
use App\Services\Admin\LiveswitchSettingService;
use App\Services\Amazon\InstanceIdProvider;
use App\Services\Amazon\InstanceService;
use Illuminate\Http\RedirectResponse;

class LiveswitchSettingController extends AdminController
{
	protected $permission = 'settings/liveswitch';
	protected $view_path = 'admin.settings-liveswitch.';
	protected $page;
	protected $service;

    /* @var InstanceService */
    protected $instanceService;

    /* @var InstanceIdProvider */
    protected $instanceIdProvider;

    public function __construct(
	    LiveswitchSettingService $service,
        InstanceService $instanceService,
        InstanceIdProvider $instanceIdProvider
    ) {
		$this->middleware('auth.permission:' . $this->permission);
		$this->service = $service;
		$this->page = [[$this->permission, 'Settings'], [$this->permission, 'Liveswitch Settings']];
        $this->instanceService = $instanceService;
        $this->instanceIdProvider = $instanceIdProvider;
    }

    public function showEdit()
    {
    	return view($this->view_path . 'manage', [
    	    'entry' => $this->service->getEntry(1),
            'instanceIsRunning' => $this->instanceService->isRunning(
                $this->instanceIdProvider->getMain()
            ),
            'page' => array_merge($this->page, [['', 'Edit']])
        ]);
    }

    public function edit(LiveswitchSettingRequest $request)
    {
        $this->service->editEntry($request, 1);

        return $this->createRedirectResponse('Your changes have been saved.');
    }

    public function startAwsInstance()
    {
        $res = $this->instanceService->start($this->instanceIdProvider->getMain(), true);

        return $this->createRedirectResponse(
            sprintf('Aws instance %s started', $res ? 'was' : 'was not'),
            $res ? 'success' : 'error'
        );
    }

    public function stopAwsInstance()
    {
        $res = $this->instanceService->stop($this->instanceIdProvider->getMain());

        return $this->createRedirectResponse(
            sprintf('Aws instance %s stopped', $res ? 'was' : 'was not'),
            $res ? 'success' : 'error'
        );
    }

    protected function createRedirectResponse(string $message, string $status = 'success'): RedirectResponse
    {
        return redirect()
            ->action('Admin\LiveswitchSettingController@showEdit')
            ->with("flash_message_$status", $message);
    }
}
