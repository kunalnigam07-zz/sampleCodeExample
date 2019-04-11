<?php

namespace App\Http\Middleware;

use Closure;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  String  $perm
     * @return mixed
     */
    public function handle($request, Closure $next, $perm)
    {
        if (!in_array($perm, $request->session()->get('admin_permissions')) && !in_array('all', $request->session()->get('admin_permissions'))) {
            return redirect()->to('/admin/login')->with('flash_message_error', 'Unauthorised access.');
        }

        return $next($request);
    }
}
