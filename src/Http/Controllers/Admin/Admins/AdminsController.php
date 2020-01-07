<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/26
 * Time: 12:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Admins;


use Z1px\App\Http\Controllers\AdminController;

class AdminsController extends AdminController
{

    /**
     * 账号列表
     */
    public function index()
    {
        return view('admin.admins.admins.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'username',
                    'title' => app('admins_service')->attributes('username'),
                    'type' => 'text',
                ],
                [
                    'name' => 'status',
                    'title' => app('admins_service')->attributes('status'),
                    'type' => 'select',
                    'list' => app('admins_service')->list_status,
                ],
                [
                    'name' => 'start_time',
                    'title' => app('admins_service')->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app('admins_service')->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('data', app('admins_service')->toList())
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加账号
     */
    public function add()
    {
        if(request()->ajax()){
            $result = app('admins_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.admins.update') ? route('admin.admins.update', ['id' => $result['data']['id']]) : url('admins.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        $form = [
            [
                'name' => 'username',
                'title' => app('admins_service')->attributes('username'),
                'type' => 'text',
            ],
            [
                'name' => 'nickname',
                'title' => app('admins_service')->attributes('nickname'),
                'type' => 'text',
            ],
            [
                'name' => 'avatar',
                'title' => app('admins_service')->attributes('avatar'),
                'type' => 'file_image',
            ],
            [
                'name' => 'mobile',
                'title' => app('admins_service')->attributes('mobile'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'title' => app('admins_service')->attributes('email'),
                'type' => 'text',
            ],
            [
                'name' => 'password',
                'title' => app('admins_service')->attributes('password'),
                'value' => '',
                'type' => 'password',
            ],
            [
                'name' => 'password_confirmation',
                'title' => '确认密码',
                'value' => '',
                'type' => 'password',
            ],
            [
                'name' => 'status',
                'title' => app('admins_service')->attributes('status'),
                'value' => 1,
                'type' => 'radio',
                'list' => app('admins_service')->list_status,
            ],
            ['type' => 'line'],
            [
                'name' => 'role_id',
                'title' => app('admins_service')->attributes('role_id'),
                'type' => 'checkbox',
                'list' => app('roles_service')->toListAll(),
            ],
        ];
        return view('admin.admins.admins.add')
            ->with('input_form', str_replace('upload-image', 'upload-image thumb-avatar', $this->buildForm($form, null, 'post', 'multipart/form-data')))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 修改账号
     */
    public function update()
    {
        if(request()->ajax()){
            return $this->json(app('admins_service')->toUpdate());
        }
        $data = app('admins_service')->toInfo();
        $form = [
            [
                'name' => 'username',
                'title' => app('admins_service')->attributes('username'),
                'value' => $data->username,
                'type' => 'text',
            ],
            [
                'name' => 'nickname',
                'title' => app('admins_service')->attributes('nickname'),
                'value' => $data->nickname,
                'type' => 'text',
            ],
            [
                'name' => 'avatar',
                'title' => app('admins_service')->attributes('avatar'),
                'value' =>  $data->avatar ?? '',
                'type' => 'file_image',
            ],
            [
                'name' => 'mobile',
                'title' => app('admins_service')->attributes('mobile'),
                'value' => $data->mobile,
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'title' => app('admins_service')->attributes('email'),
                'value' => $data->email,
                'type' => 'text',
            ],
            [
                'name' => 'password',
                'title' => app('admins_service')->attributes('password'),
                'value' => '',
                'type' => 'password',
            ],
            [
                'name' => 'password_confirmation',
                'title' => '确认密码',
                'value' => '',
                'type' => 'password',
            ],
            [
                'name' => 'status',
                'title' => app('admins_service')->attributes('status'),
                'value' => $data->status,
                'type' => 'radio',
                'list' => app('admins_service')->list_status,
            ],
            ['type' => 'line'],
            [
                'name' => 'role_id',
                'title' => app('admins_service')->attributes('role_id'),
                'value' => app('admins_roles_service')->where('admin_id', $data->id)->pluck('role_id')->toArray(),
                'type' => 'checkbox',
                'list' => app('roles_service')->toListAll(),
            ],
            [
                'name' => 'id',
                'title' => app('admins_service')->attributes('id'),
                'value' => $data->id,
                'type' => 'hidden',
            ]
        ];
        return view('admin.admins.admins.update')
            ->with('input_form', str_replace('upload-image', 'upload-image thumb-avatar', $this->buildForm($form, null, 'post', 'multipart/form-data')))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 删除账号
     */
    public function delete()
    {
        if(request()->ajax()) {
            return $this->json(app('admins_service')->toDelete());
        }
        return $this->error();
    }

    /**
     * 恢复账号
     */
    public function restore()
    {
        if(request()->ajax()) {
            return $this->json(app('admins_service')->toRestore());
        }
        return $this->error();
    }

    /**
     * 导出账号
     */
    public function export()
    {
        if(request()->ajax()) {
            return $this->json(app('admins_service')->toList());
        }
        return $this->error();
    }

}
