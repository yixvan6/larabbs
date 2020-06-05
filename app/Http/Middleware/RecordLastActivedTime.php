<?php

namespace App\Http\Middleware;

use Closure;

class RecordLastActivedTime
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
        // 如果是登录用户，则记录活跃时间
        if (\Auth::check()) {
            \Auth::user()->recordLastActivedAt();
        }

        return $next($request);
    }
}
