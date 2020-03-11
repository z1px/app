<?php

namespace Z1px\App\Http\Services\Users;


use Z1px\App\Models\Users\UsersBehaviorModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\Tool\IP;
use Z1px\Tool\Server;

class UsersBehaviorService extends UsersBehaviorModel
{

    use ToAdd, ToList;

    /**
     * 新增用户行为日志记录前修改参数
     * @param $params
     * @param array $data
     * @return array
     */
    protected function toAddParams(array $params, array $data = [])
    {
        $params = array_merge($params, [
            'user_id' => 0, // 用户ID
            'title' => '', // 行为名称
            'route_name' => request()->route() ? request()->route()->getName() : '', // 路由名称
            'url' => app()->runningInConsole() ? request()->input('command', 'console') : request()->getUri(), // 请求地址
            'params' => request()->all() ?: null, // 请求参数
            'ip' => request()->getClientIp(), // 请求IP
            'area' => IP::format(request()->getClientIp()), // IP区域
            'platform' => request()->header('platform') ?: Server::getPlatform(), // 客户端平台
            'model' => request()->header('model') ?: Server::getModel(), // 设备型号
            'runtime' => microtime(true) - request()->server('REQUEST_TIME_FLOAT'), // 运行时间，单位秒
        ], $data);

        return $params;
    }

}
