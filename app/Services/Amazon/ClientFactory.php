<?php

namespace App\Services\Amazon;

use App\Models\Setting;
use Aws\Ec2\Ec2Client;

class ClientFactory
{
    /**
     * @var bool
     */
    private static $envVarsIsInitialized = false;

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
     * @return Ec2Client
     */
    public function createEc2(): Ec2Client
    {
        if (!self::$envVarsIsInitialized) {
            $settings = $this->setting->findOrFail(1);
            putenv("AWS_ACCESS_KEY_ID={$settings->aws_access_key_id}");
            putenv("AWS_SECRET_ACCESS_KEY={$settings->aws_secret_access_key}");

            self::$envVarsIsInitialized = true;
        }

        return new Ec2Client([
            'region'  => 'eu-west-2',
            'version' => '2016-11-15'
        ]);
    }
}
