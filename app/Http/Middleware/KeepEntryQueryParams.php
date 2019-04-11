<?php

namespace App\Http\Middleware;

use Closure;

class KeepEntryQueryParams
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

      $p_res = $request->query('res');
      $p_fps = $request->query('fps');
      $p_mode = $request->query('live_mode');

      if ($p_res) {
        $request->session()->set('live_res', $p_res);
      } else if ($request->exists('res')) {
        $request->session()->set('live_res', '');
      }

      if ($p_fps) {
        $request->session()->set('live_fps', $p_fps);
      } else if ($request->exists('fps')) {
        $request->session()->set('live_fps', '');
      }

      if ($p_mode) {
        $request->session()->set('live_mode', $p_mode);
      } else if ($request->exists('live_mode')) {
        $request->session()->set('live_mode', '');
      }

      return $next($request);
    }
}
