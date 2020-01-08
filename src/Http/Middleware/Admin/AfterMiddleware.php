<?php

namespace Z1px\App\Http\Middleware\Admin;

use Z1px\App\Http\Services\Admins\AdminsBehaviorService;
use Z1px\App\Http\Services\Admins\AdminsLoginService;
use Closure;

/**
 * 后置中间件
 * Class AfterMiddleware
 * @package App\Http\Middleware
 */
class AfterMiddleware
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
        $response = $next($request);

        // 执行一些任务
        dump("AfterMiddleware");
//        app(AdminsLoginService::class)->toAdd();
//        app(AdminsBehaviorService::class)->toAdd();
//        app(AdminsBehaviorService::class)->toAdd(['response' => $response->getContent()]);

        return $response;
    }
}
