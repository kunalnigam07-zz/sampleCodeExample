<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use AuthHelper;
use NetworkHelper;

class BanCheck
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
        if ($request->has('email')) {
            $banned = AuthHelper::banCheck('email', $request->get('email'));
            if ($banned) {
                return redirect()->back()->with('flash_message_error', 'Email could not be saved. Please contact us ASAP.');
            }
        }

        if ($request->has('bank_account_number')) {
            $banned = AuthHelper::banCheck('bank_account_number', $request->get('bank_account_number'));
            if ($banned) {
                return redirect()->back()->with('flash_message_error', 'Bank details could not be saved. Please contact us ASAP.');
            }
        }

        if ($request->has('paypal_email')) {
            $banned = AuthHelper::banCheck('paypal_email', $request->get('paypal_email'));
            if ($banned) {
                return redirect()->back()->with('flash_message_error', 'PayPal details could not be saved. Please contact us ASAP.');
            }
        }

        $banned = AuthHelper::banCheck('ip', NetworkHelper::getIP());
        if ($banned) {
            return redirect()->back()->with('flash_message_error', 'Details could not be saved. Please contact us ASAP.');
        }

        return $next($request);
    }
}
