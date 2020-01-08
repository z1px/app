<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/6
 * Time: 10:55 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Logs;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\Admins\AdminsBehaviorService;
use Z1px\App\Http\Services\Users\UsersBehaviorService;

class BehaviorController extends Controller
{

    private $admins_behavior_model = AdminsBehaviorService::class;
    private $users_behavior_model = UsersBehaviorService::class;

    /**
     * 管理员日志列表
     */
    public function admins()
    {
        if(request()->ajax()){
            return $this->json(app($this->admins_behavior_model)->toList());
        }
        return $this->error();
    }

    /**
     * 管理员日志列表
     */
    public function users()
    {
        if(request()->ajax()){
            return $this->json(app($this->users_behavior_model)->toList());
        }
        return $this->error();
    }
}
