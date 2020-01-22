<?php

namespace Z1px\App\Http\Services\Admins;


use Z1px\App\Models\Admins\RolesModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToListAll;
use Z1px\App\Traits\Eloquent\ToUpdate;

class RolesService extends RolesModel
{

    use ToInfo, ToAdd, ToUpdate, ToDelete, ToList, ToListAll;

    /**
     * 获取权限
     * @return array
     * @throws \Exception
     */
    public function getPermissions()
    {
        $data = $this->toInfo();
        return [
            'code' => 1,
            'message' => '权限获取成功',
            'data' =>  $data->permissions
        ];
    }

    /**
     * 权限设置
     * @return array
     * @throws \Exception
     */
    public function setPermissions()
    {
        $data = $this->toInfo();

        $list_permission_ids = request()->input('permission_ids');

        try {
            if(empty($list_permission_ids)){
                $data->permissions()->detach(); // 删除所有中间表ID
            }else{
                $list_pivot_permission_ids = $data->permissions()->pluck('permission_id')->toArray(); // 中间表已存在属性ID
                if(empty($list_pivot_permission_ids)){
                    $data->permissions()->attach($list_permission_ids);
                }else{
                    $detach = array_diff($list_pivot_permission_ids, $list_permission_ids);
                    if(!empty($detach)){
                        $data->permissions()->detach($detach); // 删除中间表中未选择的ID
                    }
                    $attach = array_diff($list_permission_ids, $list_pivot_permission_ids);
                    if(!empty($attach)){
                        $data->permissions()->attach($attach); // 添加中间表中未添加的ID
                    }
                    unset($detach, $attach);
                }
                unset($list_pivot_permission_ids);
            }
            unset($list_permission_ids);
        }catch (\Exception $exception){
            return [
                'code' => 0,
                'message' => '操作异常'
            ];
        }

        return [
            'code' => 1,
            'message' => '设置成功',
            'data' =>  $data->permissions
        ];
    }

}
