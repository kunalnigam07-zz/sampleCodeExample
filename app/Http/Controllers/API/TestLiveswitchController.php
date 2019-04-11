<?php

namespace App\Http\Controllers\API;

use App\Services\API\TestLiveswitchService;
use Illuminate\Http\Request;

class TestLiveswitchController extends APIController
{
    protected $service;

    public function __construct(TestLiveswitchService $service)
    {
        $this->service = $service;
    }

    public function generateClientRegisterToken(Request $request)
    {
        return $this->service->generateClientRegisterToken($request);
    }

    public function generateClientJoinToken(Request $request)
    {
        return $this->service->generateClientJoinToken($request);
    }
}
