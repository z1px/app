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

    protected function toListAlling(object $data): object
    {
        $data = $data->orderBy('pid', 'asc')->orderBy('id', 'asc');
        return $data;
    }

}
