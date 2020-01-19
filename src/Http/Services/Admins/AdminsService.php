<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/9/29
 * Time: 6:17 下午
 */


namespace Z1px\App\Http\Services\Admins;


use Illuminate\Support\Facades\Hash;
use Z1px\App\Models\Admins\AdminsModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class AdminsService extends AdminsModel
{

    use ToAdd, ToUpdate, ToInfo, ToDelete, ToRestore, ToList;

    protected function toUpdating()
    {
        if(request()->input('old_password') && !Hash::check(request()->input('old_password'), $this->getOriginal('password'))){
            return [
                'code' => 0,
                'message' => '密码错误'
            ];
        }
        if($this->getAttribute('password') && $this->getOriginal('password') !== $this->getAttribute('password')){
            $this->setAttribute('access_token', null);
        }
        return $this;
    }

}
