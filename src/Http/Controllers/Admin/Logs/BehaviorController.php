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
        if(!request()->has('size')){
            request()->offsetSet('size', 10);
        }
        if(request()->ajax()) {
            $data = app($this->admins_behavior_model)->toList();
            return $this->json(['data' => $data, 'pager' => $data->appends(request()->input())->onEachSide(1)->links('pager')->render()]);
        }
        return view('admin.logs.behavior.admins')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app($this->admins_behavior_model)->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'start_time',
                    'title' => app($this->admins_behavior_model)->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app($this->admins_behavior_model)->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('data', app($this->admins_behavior_model)->toList())
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
            $data = app($this->users_behavior_model)->toList();
            return $this->json(['data' => $data, 'pager' => $data->appends(request()->input())->onEachSide(1)->links('pager')->render()]);
        }
        return view('admin.logs.behavior.users')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app($this->users_behavior_model)->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'start_time',
                    'title' => app($this->users_behavior_model)->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app($this->users_behavior_model)->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('data', app($this->users_behavior_model)->toList())
            ->with('list_menu', app('menu_logic')->toList());
    }
}
