<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\Admin\LoginService;

class LoginController extends AdminController
{
	protected $service;
	
	public function __construct(LoginService $service)
    {
		$this->service = $service;
    }

    public function index()
    {
        $page_title = 'Login';
        return view('admin.login.index', compact('page_title'));
    }

    public function login(Request $request)
	{
		if ($this->service->login($request)) {
			return redirect()->intended('/admin');
		}
		
		return redirect()->to('/admin/login')->with('flash_message_error', 'Invalid login, please try again.');
	}
	
	public function logout(Request $request)
	{
		$this->service->logout($request);
		return redirect()->to('/admin/login');
	}

	public function moxiemanager()
	{
		return $this->service->moxiemanager();
	}
}
