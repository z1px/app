<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/5
 * Time: 9:44 上午
 */


namespace Z1px\App\Http\Services\Users;


use Z1px\App\Models\Users\UsersPassportsModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class UsersPassportsService extends UsersPassportsModel
{

    use ToAdd, ToUpdate, ToInfo, ToDelete, ToRestore;

}
