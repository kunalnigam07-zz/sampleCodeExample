<?php

namespace App\Services\Admin;

use Auth;
use Session;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Permission;

class LoginService extends AdminService
{
    public function login($request)
    {
    	$credentials = $request->only('email', 'password');
		$credentials['status'] = 1;
        $credentials['user_type'] = 1;

		if (Auth::attempt($credentials, $request->has('rememberme'))) {
			Auth::user()->update(['login_at' => Carbon::now()]);
            $perms = array_flatten(Auth::user()->permissions->pluck('permission')->all());
            $request->session()->put('admin_permissions', $perms);
            
			return true;
		}

		return false;
    }

    public function logout($request)
    {
        $request->session()->forget('admin_permissions');
    	Auth::logout();
    }

    public function moxiemanager()
    {
        if (!Auth::guest() && Auth::user()->user_type == 1) {
            return 'yes';
        } else {
            return 'no';
        }
    }
}
