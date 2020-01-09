<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/9/27
 * Time: 10:23 上午
 */


namespace Z1px\App\Http\Controllers\Admin;


use Z1px\App\Http\Controllers\Controller;

class IndexController extends Controller
{

    public function index()
    {
        return view('admin.index');
    }

    public function login()
    {
        if(request()->ajax()) {
            return $this->json(['message' => '登录成功']);
        }
        return $this->error();
    }

}
