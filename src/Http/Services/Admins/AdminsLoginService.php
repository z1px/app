<?php

namespace Z1px\App\Http\Services\Admins;


use Z1px\App\Models\Admins\AdminsLoginModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\Tool\IP;
use Z1px\Tool\Server;

class AdminsLoginService extends AdminsLoginModel
{

    use ToAdd, ToList;

    /**
     * 新增管理员登录日志记录前修改参数
     * @param $params
     * @param array $data
     * @return array
     */
    protected function toAddParams(array $params, array $data = [])
    {
        $params = array_merge($params, [
            'admin_id' => 0, // 管理员ID
            'nickname' => '', // 昵称
            'username' => '', // 账号
            'mobile' => '', // 手机号
            'email' => '', // 邮箱号
            'route_name' => request()->route() ? request()->route()->getName() : '', // 路由名称
            'route_action' => request()->route() ? request()->route()->getActionName() : '', // 路由方法
            'url' => app()->runningInConsole() ? request()->input('command', 'console') : request()->getUri(), // 请求地址
            'method' => request()->getRealMethod(), // 请求类型
            'ip' => request()->getClientIp(), // 请求IP
            'area' => IP::format(request()->getClientIp()), // IP区域
            'user_agent' => request()->userAgent(), // 浏览器信息
            'device' => Server::isMobile() ? 'mobile' : 'pc', // 设备
        ], $data);

        return $params;
    }

}
