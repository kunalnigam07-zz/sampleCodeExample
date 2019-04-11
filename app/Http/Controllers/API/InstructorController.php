<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\API\InstructorService;

class InstructorController extends APIController
{
	protected $service;

	public function __construct(InstructorService $service)
    {
		$this->service = $service;
	}

    public function join(Request $request)
    {
    	return $this->service->join($request);
    }

}
