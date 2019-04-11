<?php

namespace App\Services\Amazon;

class StatusCodes
{
    const PENDING = 0;
    const RUNNING = 16;
    const SHUTTING_DOWN = 32;
    const TERMINATED = 48;
    const STOPPING = 64;
    const STOPPED = 80;
}
