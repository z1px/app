<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2020/3/4
 * Time: 1:59 上午
 */


namespace Z1px\App\Http\Services\Users;


use Z1px\App\Models\Users\UsersThirdModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class UsersThirdService extends UsersThirdModel
{

    use ToAdd, ToUpdate, ToInfo, ToDelete, ToRestore;

}
