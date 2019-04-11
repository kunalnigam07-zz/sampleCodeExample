<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AuthenticateMember
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->user_type != 3) {
            $request->session()->put('tried_to_access', $request->url());

            return redirect()->route('web.home')->with('flash_message_error', 'Please log in first.')->with('show_modal', 'modal-login');
            //return redirect('/join?type=member')->with('flash_message_error', 'Please register to access the requested page.');
        }

        return $next($request);
    }
}
