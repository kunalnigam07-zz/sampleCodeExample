<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\Amazon\InstanceIdProvider;
use App\Services\Amazon\InstanceService;
use App\Services\Repository\ClassEventRepository;
use App\Services\Web\RelClassAwsInstanceManager;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AwsInstanceTurnOnOff extends Command
{
    //Argument {wait-time} is not being used, MINUTES_FOR_CLASS_TO_BEGIN is
    //It's been re-added because the signature is not changing correctly on deployment
    protected $signature = 'aws:instance:turn-on-off {wait-time}';

    protected $description = 'Turn on/off an AWS instance if there isn\'t a class in the next ' . MINUTES_FOR_CLASS_TO_BEGIN . ' minutes';

    /* @var InstanceIdProvider */
    protected $instanceIdProvider;

    /* @var InstanceService */
    protected $instanceService;

    /* @var ClassEventRepository */
    protected $classEventRepository;

    /* @var RelClassAwsInstanceManager */
    protected $relClassAwsInstanceManager;

    /* @var Setting */
    protected $setting;

    /**
     * @param InstanceIdProvider $instanceIdProvider
     * @param InstanceService $instanceService
     * @param ClassEventRepository $classEventRepository
     * @param RelClassAwsInstanceManager $relClassAwsInstanceManager
     * @param Setting $setting
     */
    public function __construct(
        InstanceIdProvider $instanceIdProvider,
        InstanceService $instanceService,
        ClassEventRepository $classEventRepository,
        RelClassAwsInstanceManager $relClassAwsInstanceManager,
        Setting $setting
    )
    {
        parent::__construct();
        $this->instanceIdProvider = $instanceIdProvider;
        $this->instanceService = $instanceService;
        $this->classEventRepository = $classEventRepository;
        $this->relClassAwsInstanceManager = $relClassAwsInstanceManager;
        $this->setting = $setting;
    }

    public function handle()
    {
        //Get the number of classes, if any, about to start in MINUTES_FOR_CLASS_TO_BEGIN (constant can be found
        // in \config\constants.php)
        $soonClassesCount = $this->classEventRepository->countByRangeOfClassAt(
            new \DateTime(),
            (new \DateTime())->add(new \DateInterval("PT" . MINUTES_FOR_CLASS_TO_BEGIN . "M"))
        );

        $instanceId = $this->instanceIdProvider->getMain();
        $instanceIsRunning = $this->instanceService->isRunning($instanceId);
        $settings = $this->setting->findOrFail(1);

        //If instance not running, set aws_manual_started flag to null
        if (!$instanceIsRunning) {
            $settings->aws_manual_started = null;
            $settings->save();
        }

        // Don't stop if manually started < MINUTES_TO_WAIT_IF_MANUAL_AWS_START (constant can be found \config\constants.php)
        $isManualDontStop = false;
        if ($instanceIsRunning && $settings->aws_manual_started) {
            $currentTime = new \DateTime();
            $manualRunWaitUntil = (new \DateTime($settings->aws_manual_started))->add(new \DateInterval("PT" . MINUTES_TO_WAIT_IF_MANUAL_AWS_START . "M"));
            $isManualDontStop = $currentTime < $manualRunWaitUntil;
            \Log::info(\Carbon\Carbon::now()->toDateTimeString() . " Manually-started AWS Instance ($instanceId) 
                    can be stopped earliest by " . $manualRunWaitUntil->format('Y-m-d H:i:s'));
        }

        \Log::info("Classes about to begin in " .  MINUTES_TO_WAIT_IF_MANUAL_AWS_START . " minutes = $soonClassesCount");
        \Log::info("Is instance running? : " .  $instanceIsRunning);
        \Log::info("Is instance free? : " .  $this->relClassAwsInstanceManager->isInstanceFree($instanceId));
        if($settings->aws_manual_started) {
            \Log::info("Is it time to stop manually-started instance? : " .  !$isManualDontStop);
        }

        //If no classes about to being in MINUTES_FOR_CLASS_TO_BEGIN and instance is not being used and/or not
        // manually started, stop the instance
        if ($soonClassesCount == 0 &&
            $instanceIsRunning &&
            $this->relClassAwsInstanceManager->isInstanceFree($instanceId) &&
            !$isManualDontStop
        ) {
            \Log::info(\Carbon\Carbon::now()->toDateTimeString() . " AWS Instance ($instanceId) is about to be stopped 
                    since it was non-manually-started, is free to be stopped, and there are $soonClassesCount upcoming classes");
            $result = $this->instanceService->stop($instanceId);
            \Log::info(\Carbon\Carbon::now()->toDateTimeString() . " And as such, $result AWS Instance(s) was STOPPED");
            return;
        }

        //If there are classes about to begin within MINUTES_FOR_CLASS_TO_BEGIN and instance isn't running, start it
        if ($soonClassesCount > 0 && !$instanceIsRunning) {
            \Log::info(\Carbon\Carbon::now()->toDateTimeString() . " An AWS instance is about to be started since there 
                    are $soonClassesCount upcoming classes");
            $result = $this->instanceService->start($instanceId);
            \Log::info(\Carbon\Carbon::now()->toDateTimeString() . " And as such, $result AWS Instance(s) was STARTED");
            return;
        }
    }
}
