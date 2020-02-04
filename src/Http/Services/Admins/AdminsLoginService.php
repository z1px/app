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
        $params = array_merge([
            'admin_id' => request()->login ? request()->login->id : 0, // 管理员ID
            'nickname' => request()->login ? request()->login->nickname : '', // 昵称
            'username' => request()->login ? request()->login->username : '', // 账号
            'mobile' => request()->login ? request()->login->mobile : '', // 手机号
            'email' => request()->login ? request()->login->email : '', // 邮箱号
            'route_name' => request()->route() ? request()->route()->getName() : '', // 路由名称
            'url' => app()->runningInConsole() ? request()->input('command', 'console') : request()->getUri(), // 请求地址
            'params' => request()->all() ?: null, // 请求参数
            'ip' => request()->getClientIp(), // 请求IP
            'area' => IP::format(request()->getClientIp()), // IP区域
            'device' => Server::isMobile() ? 'mobile' : 'pc', // 设备
        ], $data);

        return $params;
    }

}
