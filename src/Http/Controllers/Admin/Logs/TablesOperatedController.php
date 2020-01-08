<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/5
 * Time: 1:50 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Logs;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\TablesOperatedService;

class TablesOperatedController extends Controller
{

    private $model = TablesOperatedService::class;

    /**
     * 数据库表操作日志列表
     */
    public function index()
    {
        return view('admin.logs.tables_operated.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'table',
                    'title' => app($this->model)->attributes('table'),
                    'type' => 'text',
                ],
                [
                    'name' => 'operate',
                    'title' => app($this->model)->attributes('operate'),
                    'type' => 'select',
                    'multiple' => 'multiple',
                    'list' => app($this->model)->list_operate,
                ],
                [
                    'name' => 'user_type',
                    'title' => app($this->model)->attributes('user_type'),
                    'type' => 'select',
                    'list' => app($this->model)->list_user_type,
                ],
                [
                    'name' => 'start_time',
                    'title' => app($this->model)->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app($this->model)->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('data', app($this->model)->toList())
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 数据库表操作日志信息
     */
    public function info()
    {
        if(request()->ajax()) {
            return $this->json(['data' => app($this->model)->toInfo()]);
        }
        return $this->error();
    }

}
