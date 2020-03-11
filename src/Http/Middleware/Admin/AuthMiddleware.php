<?php

namespace Z1px\App\Http\Middleware\Admin;

use Closure;
use Z1px\App\Http\Services\Admins\PermissionsService;
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
                'code' => -1,
                'message' => '未登录！',
            ]);
        }

        // 登录判断
        $data = app(AdminsModel::class)->select(['id', 'username', 'nickname', 'mobile', 'email', 'file_id', 'status', 'login_at', 'access_token'])
            ->where('access_token', $access_token)
            ->first();

        if(empty($data)){
            return result([
                'code' => -1,
                'message' => '登录已过期或未登录'
            ]);
        }
        if(1 !== $data->status){
            return [
                'code' => 0,
                'message' => '该账号已被禁用',
            ];
        }
        request()->admin = $data;

        // 权限判断
        if(1 === $data->id){
            $data_permissions = app(PermissionsService::class)->toListAll();
        }else{
            $data_permissions = $data->permissions()->where('status', 1)->get();

            $list_roles = $data->roles()->where('status', 1)->get();
            if(count($list_roles) > 0){
                foreach ($list_roles as $role){
                    $data_permissions = $data_permissions->merge($role->permissions()->where('status', 1)->get());
                }
            }
            unset($list_roles);
        }
        request()->permissions = $data_permissions;

        $white_routes = ['admin.info', 'admin.updateInfo', 'admin.rules', 'admin.upload', 'admin.logout'];
        $route = request()->route() ? request()->route()->getName() : '';

        if(!in_array($route, $white_routes) && !$data_permissions->contains('route_name', $route)){
            return result([
                'code' => 0,
                'message' => '无权限！！！'
            ]);
        }
        unset($data, $data_permissions, $white_routes, $route);

        return $next($request);
    }
}
