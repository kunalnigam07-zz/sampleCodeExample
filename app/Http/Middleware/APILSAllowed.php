<?php

namespace App\Http\Middleware;

use AuthHelper;
use Closure;

class APILSAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  String  $perm
     * @return mixed
     */
    public function handle($request, Closure $next) {
      if (!AuthHelper::isLoggedInMemberOrInstructor()) {
        $error = [
            'status' => 'error',
            'errors' => [
                [
                    'message' => 'Not authenticated'
                ]
            ]
        ];
        
        return response()->json($error, 401);
      } else {
        return $next($request);
      }
    }
}
