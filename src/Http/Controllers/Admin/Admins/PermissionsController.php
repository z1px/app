<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/19
 * Time: 3:02 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Admins;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\Admins\PermissionsService;

class PermissionsController extends Controller
{

    private $model = PermissionsService::class;

    /**
     * 权限列表
     */
    public function index()
    {
        if(request()->ajax()){
            return $this->json(['data' => app($this->model)->toListAll()]);
        }
        return view('admin.admins.permissions.index')
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 权限信息
     */
    public function getRouteActionByRouteName()
    {
        if(request()->ajax()) {
            return $this->json(['data' => app($this->model)->getRouteActionByRouteName(request()->input('route_name'))]);
        }
        return $this->error();
    }

    /**
     * 添加权限
     */
    public function add()
    {
        if(request()->has('_form')){
            $form = [
                [
                    'name' => 'title',
                    'title' => app($this->model)->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'route_name',
                    'title' => app($this->model)->attributes('route_name'),
                    'type' => 'text',
                ],
                [
                    'name' => 'route_action',
                    'title' => app($this->model)->attributes('route_action'),
                    'type' => 'text',
                ],
                [
                    'name' => 'icon',
                    'title' => app($this->model)->attributes('icon'),
                    'type' => 'text',
                ],
                [
                    'name' => 'show',
                    'title' => app($this->model)->attributes('show'),
                    'type' => 'radio',
                    'list' => app($this->model)->list_show,
                ],
                [
                    'name' => 'status',
                    'title' => app($this->model)->attributes('status'),
                    'value' => 1,
                    'type' => 'radio',
                    'list' => app($this->model)->list_status,
                ]
            ];
            return $this->json([
                'title' => '添加权限',
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'title' => '权限名称不能为空',
                    'route_name' => '路由名称不能为空',
                    'route_action' => '路由方法不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app($this->model)->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.permissions.update') ? route('admin.permissions.update', ['id' => $result['data']['id']]) : url('permissions.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        return $this->error();
    }

    /**
     * 修改权限
     */
    public function update()
    {
        if(request()->has('_form')){
            $data = app($this->model)->toInfo();
            $form = [
                [
                    'name' => 'title',
                    'title' => app($this->model)->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                ],
                [
                    'name' => 'route_name',
                    'title' => app($this->model)->attributes('route_name'),
                    'value' => $data->route_name,
                    'type' => 'text',
                ],
                [
                    'name' => 'route_action',
                    'title' => app($this->model)->attributes('route_action'),
                    'value' => $data->route_action,
                    'type' => 'text',
                ],
                [
                    'name' => 'icon',
                    'title' => app($this->model)->attributes('icon'),
                    'value' => $data->icon,
                    'type' => 'text',
                ],
                [
                    'name' => 'show',
                    'title' => app($this->model)->attributes('show'),
                    'value' => $data->show,
                    'type' => 'radio',
                    'list' => app($this->model)->list_show,
                ],
                [
                    'name' => 'status',
                    'title' => app($this->model)->attributes('status'),
                    'value' => $data->status,
                    'type' => 'radio',
                    'list' => app($this->model)->list_status,
                ],
                [
                    'name' => 'id',
                    'title' => app($this->model)->attributes('id'),
                    'value' => $data->id,
                    'type' => 'hidden',
                ],
            ];
            return $this->json([
                'data' => $data,
                'title' => "修改<font class='text-danger'>【{$data->title}】</font>",
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'id' => '参数错误',
                    'title' => '权限名称不能为空',
                    'route_name' => '路由名称不能为空',
                    'route_action' => '路由方法不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            return $this->json(app($this->model)->toUpdate());
        }
        return $this->error();
    }

    /**
     * 删除权限
     */
    public function delete()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toDelete());
        }
        return $this->error();
    }

    /**
     * 拖拽移动权限
     */
    public function drop()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toDrop());
        }
        return $this->error();
    }
}
