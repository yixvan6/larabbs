<?php

namespace App\Http\Middleware;

use Closure;

class EnsureEmailVerified
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
        /* 3个判断同时满足，才需要验证
         * 1. 用户已登录
         * 2. 还未验证 email
         * 3. 访问的不是 email 验证相关 url 或者 退出的 url
         */
        if ($request->user() &&
            ! $request->user()->hasVerifiedEmail() &&
            ! $request->is('email/*', 'logout')) {

            // 根据客户端返回对应的内容
            return $request->expectsJson()
                        ? abort(403, 'Your email address is not verified.')
                        : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
