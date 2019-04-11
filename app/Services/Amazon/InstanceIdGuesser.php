<?php

namespace App\Services\Amazon;

class InstanceIdGuesser
{
    /* @var InstanceService */
    protected $instanceService;

    public function __construct(InstanceService $instanceService)
    {
        $this->instanceService = $instanceService;
    }

    public function byUrl(string $url): ?string
    {
        $parts = parse_url($url);

        if (!array_key_exists('host', $parts)) {
            return null;
        }

        $ip = gethostbyname($parts['host']);
        if ($ip == $parts['host']) {
            return null;
        }

        foreach ($this->instanceService->listInstances() as $instance) {
            if ($instance['ip'] == $ip) {
                return $instance['id'];
            }
        }

        return null;
    }
}
