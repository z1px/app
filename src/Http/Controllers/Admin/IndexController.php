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
        return view('admin.index.index')
            ->with('list_menu', app('menu_logic')->toList());
    }

    public function login()
    {
        return view('admin.index.login');
    }
}
