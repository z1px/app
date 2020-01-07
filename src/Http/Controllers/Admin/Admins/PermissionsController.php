<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/19
 * Time: 3:02 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Admins;


use Z1px\App\Http\Controllers\AdminController;

class PermissionsController extends AdminController
{

    /**
     * 权限列表
     */
    public function index()
    {
        if(request()->ajax()){
            return $this->json(['data' => app('permissions_service')->toListAll()]);
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
            return $this->json(['data' => app('permissions_service')->getRouteActionByRouteName(request()->input('route_name'))]);
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
                    'title' => app('permissions_service')->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'route_name',
                    'title' => app('permissions_service')->attributes('route_name'),
                    'type' => 'text',
                ],
                [
                    'name' => 'route_action',
                    'title' => app('permissions_service')->attributes('route_action'),
                    'type' => 'text',
                ],
                [
                    'name' => 'icon',
                    'title' => app('permissions_service')->attributes('icon'),
                    'type' => 'text',
                ],
                [
                    'name' => 'show',
                    'title' => app('permissions_service')->attributes('show'),
                    'type' => 'radio',
                    'list' => app('permissions_service')->list_show,
                ],
                [
                    'name' => 'status',
                    'title' => app('permissions_service')->attributes('status'),
                    'value' => 1,
                    'type' => 'radio',
                    'list' => app('permissions_service')->list_status,
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
            $result = app('permissions_service')->toAdd();
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
            $data = app('permissions_service')->toInfo();
            $form = [
                [
                    'name' => 'title',
                    'title' => app('permissions_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                ],
                [
                    'name' => 'route_name',
                    'title' => app('permissions_service')->attributes('route_name'),
                    'value' => $data->route_name,
                    'type' => 'text',
                ],
                [
                    'name' => 'route_action',
                    'title' => app('permissions_service')->attributes('route_action'),
                    'value' => $data->route_action,
                    'type' => 'text',
                ],
                [
                    'name' => 'icon',
                    'title' => app('permissions_service')->attributes('icon'),
                    'value' => $data->icon,
                    'type' => 'text',
                ],
                [
                    'name' => 'show',
                    'title' => app('permissions_service')->attributes('show'),
                    'value' => $data->show,
                    'type' => 'radio',
                    'list' => app('permissions_service')->list_show,
                ],
                [
                    'name' => 'status',
                    'title' => app('permissions_service')->attributes('status'),
                    'value' => $data->status,
                    'type' => 'radio',
                    'list' => app('permissions_service')->list_status,
                ],
                [
                    'name' => 'id',
                    'title' => app('permissions_service')->attributes('id'),
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
            return $this->json(app('permissions_service')->toUpdate());
        }
        return $this->error();
    }

    /**
     * 删除权限
     */
    public function delete()
    {
        if(request()->ajax()) {
            return $this->json(app('permissions_service')->toDelete());
        }
        return $this->error();
    }

    /**
     * 拖拽移动权限
     */
    public function drop()
    {
        if(request()->ajax()) {
            return $this->json(app('permissions_service')->toDrop());
        }
        return $this->error();
    }
}
