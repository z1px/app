<?php

namespace Z1px\App\Http\Services\Admins;


use Z1px\App\Models\Admins\PermissionsModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToListAll;
use Z1px\App\Traits\Eloquent\ToUpdate;

class PermissionsService extends PermissionsModel
{

    use ToList, ToInfo, ToAdd, ToUpdate, ToDelete, ToListAll;

    /**
     * 权限列表
     */
    protected function toListAlling(object $data): object
    {
        $data = $data->select(['id', 'title', 'route_name', 'status', 'pid']);
        return $data;
    }

}
