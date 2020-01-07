<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/1
 * Time: 10:45 下午
 */


namespace Z1px\App\Http\Controllers\Admin;


use Z1px\App\Http\Controllers\AdminController;

class DemoController extends AdminController
{

    /**
     * table列表数据展示示例
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function table()
    {
        return view('admin.demo.table');
    }

    /**
     * form表单添加或修改数据示例
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function form()
    {
        return view('admin.demo.form');
    }

}
