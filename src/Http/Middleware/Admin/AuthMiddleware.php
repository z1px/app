<?php

namespace Z1px\App\Http\Middleware\Admin;

use Closure;
use Z1px\App\Models\Admins\AdminsModel;

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

        $access_token = request()->header('x-token');
        if(empty($access_token)){
            return result([
                'code' => 0,
                'message' => '未登录！',
            ]);
        }

        $data = app(AdminsModel::class)->select(['id', 'username', 'nickname', 'mobile', 'email', 'file_id', 'status', 'login_at', 'access_token'])
            ->where('access_token', $access_token)
            ->first();

        if(empty($data)){
            return result([
                'code' => 0,
                'message' => '登录已过期或未登录'
            ]);
        }

        request()->login = $data;

        return $next($request);
    }
}
