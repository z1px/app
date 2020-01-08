<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/6
 * Time: 10:55 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Logs;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\Admins\AdminsLoginService;
use Z1px\App\Http\Services\Users\UsersLoginService;

class LoginController extends Controller
{

    private $admins_login_model = AdminsLoginService::class;
    private $users_login_model = UsersLoginService::class;

    /**
     * 管理员日志列表
     */
    public function admins()
    {
        if(request()->ajax()){
            return $this->json(app($this->admins_login_model)->toList());
        }
        return $this->error();
    }

    /**
     * 管理员日志列表
     */
    public function users()
    {
        if(request()->ajax()){
            return $this->json(app($this->users_login_model)->toList());
        }
        return $this->error();
    }
}
