<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\API\MemberService;

class MemberController extends APIController
{
	protected $service;

	public function __construct(MemberService $service)
    {
		$this->service = $service;
	}

    public function joinBook(Request $request)
    {
    	return $this->service->joinBook($request);
    }

}
