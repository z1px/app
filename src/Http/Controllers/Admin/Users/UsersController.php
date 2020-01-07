<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/26
 * Time: 12:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Users;


use Z1px\App\Http\Controllers\AdminController;

class UsersController extends AdminController
{

    /**
     * 账号列表
     */
    public function index()
    {
        return view('admin.users.users.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'username',
                    'title' => app('users_service')->attributes('username'),
                    'type' => 'text',
                ],
                [
                    'name' => 'status',
                    'title' => app('users_service')->attributes('status'),
                    'type' => 'select',
                    'list' => app('users_service')->list_status,
                ],
                [
                    'name' => 'start_time',
                    'title' => app('users_service')->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app('users_service')->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('table_form', $this->buildTable(
                app('users_service')->toList(),
                [
                    'id' => 'ID',
                    'avatar' => '头像',
                    'username' => '账号',
                    'nickname' => '昵称',
                    'mobile' => '手机号',
                    'email' => '邮箱',
                    'status' => '状态',
                    'login_at' => '最后登录时间',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '账号列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [ // table操作
                        'swal-update' => 'admin.users.update', // 更新地址（弹窗模式）
                        'swal-soft-delete' => 'admin.users.delete', // 删除地址（弹窗软删除模式）
                        'swal-restore' => 'admin.users.restore', // 恢复地址
                    ],
                    'pager' => 'pager', // 分页模版
                    'total' => [], // 数据总揽
                    'tr_title' => 'username', // table表行标题，data字段
                ]
            ))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加账号
     */
    public function add()
    {
        if(request()->ajax()){
            $result = app('users_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.users.update') ? route('admin.users.update', ['id' => $result['data']['id']]) : url('users.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        $form = [
            [
                'name' => 'username',
                'title' => app('users_service')->attributes('username'),
                'type' => 'text',
            ],
            [
                'name' => 'nickname',
                'title' => app('users_service')->attributes('nickname'),
                'type' => 'text',
            ],
            [
                'name' => 'avatar',
                'title' => app('users_service')->attributes('avatar'),
                'type' => 'file_image',
            ],
            [
                'name' => 'mobile',
                'title' => app('users_service')->attributes('mobile'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'title' => app('users_service')->attributes('email'),
                'type' => 'text',
            ],
            [
                'name' => 'password',
                'title' => app('users_service')->attributes('password'),
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
                'title' => app('users_service')->attributes('status'),
                'value' => 1,
                'type' => 'radio',
                'list' => app('users_service')->list_status,
            ],
        ];
        return view('admin.users.users.add')
            ->with('input_form', str_replace('upload-image', 'upload-image thumb-avatar', $this->buildForm($form, null, 'post', 'multipart/form-data')))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 修改账号
     */
    public function update()
    {
        if(request()->ajax()){
            return $this->json(app('users_service')->toUpdate());
        }
        $data = app('users_service')->toInfo();
        $form = [
            [
                'name' => 'username',
                'title' => app('users_service')->attributes('username'),
                'value' => $data->username,
                'type' => 'text',
            ],
            [
                'name' => 'nickname',
                'title' => app('users_service')->attributes('nickname'),
                'value' => $data->nickname,
                'type' => 'text',
            ],
            [
                'name' => 'avatar',
                'title' => app('users_service')->attributes('avatar'),
                'value' =>  $data->avatar ?? '',
                'type' => 'file_image',
            ],
            [
                'name' => 'mobile',
                'title' => app('users_service')->attributes('mobile'),
                'value' => $data->mobile,
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'title' => app('users_service')->attributes('email'),
                'value' => $data->email,
                'type' => 'text',
            ],
            [
                'name' => 'password',
                'title' => app('users_service')->attributes('password'),
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
                'title' => app('users_service')->attributes('status'),
                'value' => $data->status,
                'type' => 'radio',
                'list' => app('users_service')->list_status,
            ],
            [
                'name' => 'id',
                'title' => app('users_service')->attributes('id'),
                'value' => $data->id,
                'type' => 'hidden',
            ]
        ];
        return view('admin.users.users.update')
            ->with('input_form', str_replace('upload-image', 'upload-image thumb-avatar', $this->buildForm($form, null, 'post', 'multipart/form-data')))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 删除账号
     */
    public function delete()
    {
        if(request()->ajax()) {
            return $this->json(app('users_service')->toDelete());
        }
        return $this->error();
    }

    /**
     * 恢复账号
     */
    public function restore()
    {
        if(request()->ajax()) {
            return $this->json(app('users_service')->toRestore());
        }
        return $this->error();
    }

    /**
     * 导出账号
     */
    public function export()
    {
        if(request()->ajax()) {
            return $this->json(app('users_service')->toList());
        }
        return $this->error();
    }

}
