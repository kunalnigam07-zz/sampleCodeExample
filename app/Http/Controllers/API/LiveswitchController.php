<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\API\LiveswitchService;

class LiveswitchController extends APIController
{
	protected $service;

	public function __construct(LiveswitchService $service) {
		$this->service = $service;
	}

  public function generateClientRegisterToken(Request $request) {
    return $this->service->generateClientRegisterToken($request);
  }

  public function generateClientJoinToken(Request $request) {
    return $this->service->generateClientJoinToken($request);
  }
}
