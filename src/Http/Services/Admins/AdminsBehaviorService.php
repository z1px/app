<?php

namespace Z1px\App\Http\Services\Admins;


use Z1px\App\Models\Admins\AdminsBehaviorModel;
use Z1px\App\Models\Admins\PermissionsModel;
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
            'admin_id' => request()->admin ? request()->admin->id : 0, // 管理员ID
            'title' => '', // 行为名称
            'route_name' => request()->route() ? request()->route()->getName() : '', // 路由名称
            'url' => app()->runningInConsole() ? request()->input('command', 'console') : request()->getUri(), // 请求地址
            'params' => request()->all() ?: null, // 请求参数
            'ip' => request()->getClientIp(), // 请求IP
            'area' => IP::format(request()->getClientIp()), // IP区域
            'platform' => Server::getPlatform(), // 客户端平台
            'model' => Server::getModel(), // 设备型号
            'runtime' => microtime(true) - request()->server('REQUEST_TIME_FLOAT'), // 运行时间，单位秒
        ], $data);
        if(empty($params['title']) && !empty($params['route_name'])){
            $params['title'] = app(PermissionsModel::class)->where('route_name', $params['route_name'])->value('title');
        }

        return $params;
    }

}
