<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/9/27
 * Time: 10:23 上午
 */


namespace Z1px\App\Http\Controllers\Admin;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Logics\AdminsLogic;

class IndexController extends Controller
{

    public function __construct()
    {
        $this->model = AdminsLogic::class;
    }

    public function index()
    {
        return view('admin.index');
    }

    /**
     * 登录
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        if(request()->isMethod('post')) {
            return $this->json(app($this->model)->login());
        }
        return $this->error();
    }

    /**
     * 获取登录账号信息
     * @return \Illuminate\Http\Response
     */
    public function info()
    {
        if(request()->isMethod('post')) {
            return $this->json(app($this->model)->info());
        }
        return $this->error();
    }

    /**
     * 修改登录账号信息
     * @return \Illuminate\Http\Response
     */
    public function updateInfo()
    {
        if(request()->isMethod('post')) {
            return $this->json(app($this->model)->update());
        }
        return $this->error();
    }

    /**
     * 退出登录
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        if(request()->isMethod('post')) {
            return $this->json(app($this->model)->logout());
        }
        return $this->error();
    }

}
