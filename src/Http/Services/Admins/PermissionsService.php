<?php

namespace Z1px\App\Http\Services\Admins;


use Z1px\App\Models\Admins\PermissionsModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToListAll;
use Z1px\App\Traits\Eloquent\ToUpdate;

class PermissionsService extends PermissionsModel
{

    use ToInfo, ToAdd, ToUpdate, ToDelete, ToListAll;

    /**
     * 权限列表
     */
    protected function toListAlling(object $data): object
    {
        $data = $data->select(['id', 'title', 'route_name', 'route_action', 'icon', 'sort', 'show', 'status', 'pid'])
            ->orderBy('sort', 'desc');
        return $data;
    }

    /**
     * 权限拖拽排序
     */
    public function toDrop()
    {
        $params = request()->input();

        // 参数合法性验证
        validator($params, $this->rules('drop'), $this->messages(), $this->attributes())->validate();

        $data = $this->find($params['id']);
        if(empty($data)){
            return [
                'code' => 0,
                'message' => '移动失败，数据不存在！'
            ];
        }
        $data->setBeforeAttributes($data->getAttributes());

        switch ($params['move']){
            case 'inner':
                $data->pid = $params['tid'];
                $data->sort = ($this->where('pid', $params['tid'])->max('sort') ?: 0) + 1;
                break;
            case 'prev':
                $data->pid = $params['pid'];
                $this->where('pid', $data->pid)
                    ->where(function ($query) use ($params){
                        $query->where('sort', '>', $params['sort'])
                            ->orWhere(function ($q) use ($params){
                                $q->where('sort', '=', $params['sort'])
                                    ->where('id', '<', $params['tid']);
                            });
                    })
                    ->increment('sort', 2);
                $data->sort = $params['sort'] + 1;
                break;
            case 'next':
                $data->pid = $params['pid'];
                $this->where('pid', $data->pid)
                    ->where(function ($query) use ($params){
                        $query->where('sort', '>', $params['sort'])
                            ->orWhere(function ($q) use ($params){
                                $q->where('sort', '=', $params['sort'])
                                    ->where('id', '<=', $params['tid']);
                            });
                    })
                    ->increment('sort', 2);
                $data->sort = $params['sort'] + 1;
                break;
            default:
                return [
                    'code' => 0,
                    'message' => '移动失败'
                ];
        }
        if($this->where('id', $data->id)->update(['pid' => $data->pid, 'sort' => $data->sort])){
            return [
                'code' => 1,
                'message' => '移动成功',
                'data' => $data
            ];
        }else{
            return [
                'code' => 0,
                'message' => '移动失败'
            ];
        }
    }

}
