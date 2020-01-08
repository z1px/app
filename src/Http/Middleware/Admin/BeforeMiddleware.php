<?php

namespace Z1px\App\Http\Middleware\Admin;

use Closure;

/**
 * 前置中间件
 * Class BeforeMiddleware
 * @package App\Http\Middleware
 */
class BeforeMiddleware
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
        dump("BeforeMiddleware");

        return $next($request);
    }
}
