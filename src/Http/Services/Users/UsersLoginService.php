<?php

namespace Z1px\App\Http\Services\Users;


use Z1px\App\Models\Users\UsersLoginModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\Tool\IP;
use Z1px\Tool\Server;

class UsersLoginService extends UsersLoginModel
{

    use ToAdd, ToList;

    /**
     * 新增用户登录日志记录前修改参数
     * @param $params
     * @param array $data
     * @return array
     */
    protected function toAddParams(array $params, array $data = [])
    {
        $params = array_merge($params, [
            'user_id' => 0, // 用户ID
            'nickname' => '', // 昵称
            'username' => '', // 账号
            'mobile' => '', // 手机号
            'email' => '', // 邮箱号
            'route_name' => request()->route() ? request()->route()->getName() : '', // 路由名称
            'url' => app()->runningInConsole() ? request()->input('command', 'console') : request()->getUri(), // 请求地址
            'params' => request()->all() ?: null, // 请求参数
            'ip' => request()->getClientIp(), // 请求IP
            'area' => IP::format(request()->getClientIp()), // IP区域
            'platform' => request()->header('platform') ?: Server::getPlatform(), // 客户端平台
            'model' => request()->header('model') ?: Server::getModel(), // 设备型号
            'uuid' => request()->header('uuid'), // 通用唯一识别码
        ], $data);

        return $params;
    }

}
