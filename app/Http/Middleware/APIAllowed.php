<?php

namespace App\Http\Middleware;

use Closure;

class APIAllowed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  String  $perm
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->has('key') || $request->get('key') != config('services.landingpage.key')) {
            $error = [
                'status' => 'error',
                'errors' => [
                    [
                        'field' => 'key',
                        'message' => 'Invalid API key.'
                    ]
                ]
            ];
            
            return response()->json($error, 401);
        }

        return $next($request);
    }
}
