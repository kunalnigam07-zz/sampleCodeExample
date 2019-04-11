<?php

namespace App\Classes;

use App\Models\Setting;

class TestLiveSwitch extends LiveSwitch
{
    /**
     * @param Setting $settings
     */
    protected function processSettings(Setting $settings)
    {
        $this->url = $settings->test_liveswitch_url;
        $this->key = $settings->test_liveswitch_key;
        $this->secret = $settings->test_liveswitch_secret;
    }
}
