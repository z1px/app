<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/31
 * Time: 1:39 下午
 */


namespace Z1px\App\Http\Services;


use Z1px\App\Models\TablesOperatedModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\Tool\IP;

class TablesOperatedService extends TablesOperatedModel
{

    use ToInfo, ToAdd, ToList;

    /**
     * 新增数据表操作记录前修改参数
     * @param $params
     * @param array $data
     * @return array
     */
    protected function toAddParams(array $params, object $model, string $operate, array $data = [])
    {
        $params = array_merge([
            'model' => $model->getMorphClass(), // 操作表模型
            'table' => $model->getTable(), // 操作表名称
            'tid' => $model->id ?? 0, // 操作表ID
            'operate' => $operate, // 操作类型：create-新增，delete-删除，update-修改，select-查找，restore-恢复
            'before_attr' => $model->getBeforeAttributes(), // 操作前的数据
            'after_attr' => $model->getAttributes(), // 操作后的数据
            'change_attr' => $model->getChanges(), // 修改后的数据
            'route_name' => request()->route() ? request()->route()->getName() : '', // 路由名称
            'url' => app()->runningInConsole() ? request()->input('command', 'console') : request()->getUri(), // 请求地址
            'ip' => request()->getClientIp(), // 请求IP
            'area' => IP::format(request()->getClientIp()), // IP区域
            'user_type' => 0, // 用户类型
            'user_id' => 0, // 文件创建者用户ID
            'admin_id' => 0, // 后台操作管理员ID
        ], $data);

        if(request()->admin){
            $params['user_type'] = 1;
            $params['user_id'] = request()->admin->id;
        }

        return $params;
    }

    /**
     * 获取数据库表操作日志信息后
     * @return $data
     */
    protected function toInfoed(object $data): object
    {
        is_array($data->before_attr) || $data->before_attr = [];
        is_array($data->after_attr) || $data->after_attr = [];
        is_array($data->change_attr) || $data->change_attr = [];
        $list = array_merge($data->before_attr, $data->after_attr, $data->change_attr);
        $columns = (!empty($data->model) && class_exists($data->model)) ? app($data->model)->toColumnsComment() : '';
        foreach ($list as $key=>$value){
            if(in_array($key, ['password'])){
                unset($list[$key]);
                continue;
            };
            $list[$key] = [
                'field' => $key,
                'comment' => empty($columns) ? '' : $columns->getColumn($key)->getComment(),
                'before' => $data->before_attr[$key] ?? '--',
                'after' => ('delete' === $data->operate || !isset($data->after_attr[$key])) ? '--' : $data->after_attr[$key],
                'change' => $data->change_attr[$key] ?? '--'
            ];
        }
        $data->setAttribute('list', array_values($list));
        unset($list, $columns);
        return $data;
    }

}
