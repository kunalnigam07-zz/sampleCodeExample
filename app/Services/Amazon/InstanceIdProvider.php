<?php

namespace App\Services\Amazon;

use App\Models\Setting;

class InstanceIdProvider
{
    /**
     * @var string
     */
    private $maidId;

    /**
     * @var Setting
     */
    private $setting;

    /**
     * ClientFactory constructor.
     * @param Setting $setting
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    /**
     * @return string
     */
    public function getMain(): string
    {
        if (!$this->maidId) {
            $this->maidId = $this->setting->findOrFail(1)->aws_instance_id;
        }

        return $this->maidId;
    }
}
