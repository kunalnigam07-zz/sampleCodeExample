<?php

namespace App\Services\Amazon;

use App\Models\Setting;
use Aws\Result;
use Psr\Log\LoggerInterface;

class InstanceService
{
    /* @var ClientFactory */
    protected $clientFactory;

    /* @var Setting */
    protected $setting;

    /* @var LoggerInterface */
    protected $logger;

    /**
     * @param ClientFactory $clientFactory
     * @param Setting $setting
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientFactory $clientFactory,
        Setting $setting,
        LoggerInterface $logger
    ) {
        $this->clientFactory = $clientFactory;
        $this->setting = $setting;
        $this->logger = $logger;
    }

    /**
     * @param string $id
     * @param bool $isManual
     * @return bool
     */
    public function start(string $id, $isManual = false): bool
    {
        $res = $this->doSafe(function () use ($id) {
            return $this->clientFactory->createEc2()->startInstances([
                'InstanceIds' => [$id]
            ]);
        });

        if (!$this->checkStatus($res, 200)) {
            return false;
        }

        $instances = $res->get('StartingInstances');
        if (empty($instances)) {
            return false;
        }

        $instance = end($instances);
        $code = ($instance['CurrentState']['Code'] ?? -1);

        $result = in_array($code, [StatusCodes::PENDING, StatusCodes::RUNNING])
            && $instance['InstanceId'] == $id;

        if ($result && $isManual) {
            $settings = $this->setting->findOrFail(1);
            $settings->aws_manual_started = new \DateTime();
            $settings->save();
        }

        return $result;
    }

    /**
     * @param string $id
     * @param array $opts
     * @return bool
     */
    public function stop(string $id, array $opts = []): bool
    {
        $res = $this->doSafe(function () use ($id) {
            return $this->clientFactory->createEc2()->stopInstances([
                'InstanceIds' => [$id]
            ]);
        });

        if (!$this->checkStatus($res, 200)) {
            return false;
        }

        $instances = $res->get('StoppingInstances');
        if (empty($instances)) {
            return false;
        }

        $instance = end($instances);
        $code = ($instance['CurrentState']['Code'] ?? -1);

        $result = in_array($code, [StatusCodes::STOPPING, StatusCodes::STOPPED])
            && $instance['InstanceId'] == $id;

        if ($result) {
            $settings = $this->setting->findOrFail(1);
            $settings->aws_manual_started = null;
            $settings->save();
        }

        return $result;
    }

    /**
     * @param string $id
     * @param array $opts
     * @return bool
     */
    public function isRunning(string $id, array $opts = []): bool
    {
        $res = $this->doSafe(function () use ($id) {
            return $this->clientFactory->createEc2()->describeInstanceStatus([
                'InstanceIds' => [$id]
            ]);
        });

        if ($res === null) {
            return false;
        }

        $statuses = $res->get('InstanceStatuses');
        if (empty($statuses)) {
            return false;
        }

        $status = (end($statuses)['InstanceState']['Code'] ?? -1);

        return  in_array($status, [StatusCodes::PENDING, StatusCodes::RUNNING]);
    }

    public function listInstances(): array
    {
        $res = $this->doSafe(function () {
            return $this->clientFactory->createEc2()->describeInstances();
        });

        if (!$this->checkStatus($res, 200)) {
            return [];
        }

        $instances = [];

        foreach ($res->get('Reservations') as $reservations) {
            foreach ($reservations['Instances'] as $instance) {
                $instances[] = [
                    'id' => $instance['InstanceId'],
                    'ip' => $instance['PublicIpAddress'] ?? null
                ];
            }
        }

        return $instances;
    }

    protected function doSafe(callable $callback): ?Result
    {
        try {
            return $callback();
        } catch (\Throwable $throwable) {
            $this->logger->error(
                "Error of managing of AWS instance\n" . (string)$throwable
            );
        }

        return null;
    }

    protected function checkStatus(?Result $res, int $status): bool
    {
        if ($res === null) {
            return false;
        }

        $meta = $res->get('@metadata');
        if ($meta === null) {
            return false;
        }

        return ($meta['statusCode'] ?? -1) == $status;
    }
}
