<?php

namespace Z1px\App\Http\Services\Admins;


use Z1px\App\Models\Admins\AdminsBehaviorModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\Tool\IP;
use Z1px\Tool\Server;

class AdminsBehaviorService extends AdminsBehaviorModel
{

    use ToAdd, ToList;

    /**
     * 新增管理员行为日志记录前修改参数
     * @param $params
     * @param array $data
     * @return array
     */
    protected function toAddParams(array $params, array $data = [])
    {
        $params = array_merge($params, [
            'admin_id' => 0, // 管理员ID
            'title' => '', // 行为名称
            'route_name' => request()->route() ? request()->route()->getName() : '', // 路由名称
            'route_action' => request()->route() ? request()->route()->getActionName() : '', // 路由方法
            'url' => app()->runningInConsole() ? request()->input('command', 'console') : request()->getUri(), // 请求地址
            'method' => request()->getRealMethod(), // 请求类型
            'header' => request()->header(), // 请求头
            'request' => request()->all(), // 请求参数
            'response' => '', // 响应结果
            'ip' => request()->getClientIp(), // 请求IP
            'area' => IP::format(request()->getClientIp()), // IP区域
            'user_agent' => request()->userAgent(), // 浏览器信息
            'device' => Server::isMobile() ? 'mobile' : 'pc', // 设备
            'runtime' => microtime(true) - request()->server('REQUEST_TIME_FLOAT'), // 运行时间，单位秒
        ], $data);

        return $params;
    }

}
