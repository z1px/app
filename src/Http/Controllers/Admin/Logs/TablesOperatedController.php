<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/5
 * Time: 1:50 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Logs;


use Z1px\App\Http\Controllers\AdminController;

class TablesOperatedController extends AdminController
{

    /**
     * 数据库表操作日志列表
     */
    public function index()
    {
        return view('admin.logs.tables_operated.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'table',
                    'title' => app('tables_operated_service')->attributes('table'),
                    'type' => 'text',
                ],
                [
                    'name' => 'operate',
                    'title' => app('tables_operated_service')->attributes('operate'),
                    'type' => 'select',
                    'multiple' => 'multiple',
                    'list' => app('tables_operated_service')->list_operate,
                ],
                [
                    'name' => 'user_type',
                    'title' => app('tables_operated_service')->attributes('user_type'),
                    'type' => 'select',
                    'list' => app('tables_operated_service')->list_user_type,
                ],
                [
                    'name' => 'start_time',
                    'title' => app('tables_operated_service')->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app('tables_operated_service')->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('data', app('tables_operated_service')->toList())
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 数据库表操作日志信息
     */
    public function info()
    {
        if(request()->ajax()) {
            return $this->json(['data' => app('tables_operated_service')->toInfo()]);
        }
        return $this->error();
    }

}
