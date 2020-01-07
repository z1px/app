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
     * 角色列表
     */
    protected function toListAlling(object $data): object
    {
        $data = $data->select(['id', 'title', 'status'])->orderBy('id', 'asc');
        return $data;
    }

}
