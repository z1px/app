<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/6
 * Time: 10:55 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Logs;


use Z1px\App\Http\Controllers\AdminController;

class LoginController extends AdminController
{
    /**
     * 管理员日志列表
     */
    public function admins()
    {
        if(!request()->has('size')){
            request()->offsetSet('size', 10);
        }
        if(request()->ajax()) {
            $data = app('admins_login_service')->toList();
            return $this->json(['data' => $data, 'pager' => $data->appends(request()->input())->onEachSide(1)->links('pager')->render()]);
        }
        return view('admin.logs.login.admins')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'username',
                    'title' => app('admins_login_service')->attributes('username'),
                    'type' => 'text',
                ],
                [
                    'name' => 'start_time',
                    'title' => app('admins_login_service')->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app('admins_login_service')->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('data', app('admins_login_service')->toList())
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 管理员日志列表
     */
    public function users()
    {
        if(!request()->has('size')){
            request()->offsetSet('size', 10);
        }
        if(request()->ajax()) {
            $data = app('users_login_service')->toList();
            return $this->json(['data' => $data, 'pager' => $data->appends(request()->input())->onEachSide(1)->links('pager')->render()]);
        }
        return view('admin.logs.login.users')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'username',
                    'title' => app('users_login_service')->attributes('username'),
                    'type' => 'text',
                ],
                [
                    'name' => 'start_time',
                    'title' => app('users_login_service')->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app('users_login_service')->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('data', app('users_login_service')->toList())
            ->with('list_menu', app('menu_logic')->toList());
    }
}
