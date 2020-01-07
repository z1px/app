<?php

namespace Z1px\App\Http\Middleware\Admin;

use Closure;

class AuthMiddleware
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
        // 执行一些任务
//        dump("AdminAuthMiddleware");

        return $next($request);
    }
}
