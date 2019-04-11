<?php

namespace App\Services\API;

use App\Classes\TestLiveSwitch;

class TestLiveswitchService extends LiveswitchService
{
    /**
     * @param TestLiveSwitch $liveswitch
     */
    public function __construct(TestLiveSwitch $liveswitch)
    {
        parent::__construct($liveswitch);
    }
    /**
     * {@inheritdoc}
     */
    protected function getClientRegisterValidationFields()
    {
        $fields = parent::getClientRegisterValidationFields();
        // user is not required for Test LiveSwitch
        unset($fields['userId']);

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    protected function getClientJoinValidationFields()
    {
        $fields = parent::getClientJoinValidationFields();
        // user is not required for Test LiveSwitch
        unset($fields['userId']);

        return $fields;
    }
}
