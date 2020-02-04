<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/9/29
 * Time: 6:17 下午
 */


namespace Z1px\App\Http\Services\Users;


use Z1px\App\Models\Users\UsersModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class UsersService extends UsersModel
{

    use ToAdd, ToUpdate, ToInfo, ToDelete, ToList, ToRestore;

    /**
     * 获取信息前修改数据
     * @param array $params
     * @param null $id
     * @return array
     */
    protected function toInfoParams(array $params, $id=null)
    {
        if(!is_null($id)){
            $params['id'] = $id;
        }
        return $params;
    }

}
