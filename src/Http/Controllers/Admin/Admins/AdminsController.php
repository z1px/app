<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/26
 * Time: 12:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Admins;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\Admins\AdminsRolesService;
use Z1px\App\Http\Services\Admins\AdminsService;
use Z1px\App\Http\Services\Admins\RolesService;

class AdminsController extends Controller
{

    private $model = AdminsService::class;
    private $roles_model = RolesService::class;
    private $admins_roles_model = AdminsRolesService::class;

    /**
     * 账号列表
     */
    public function index()
    {
        return view('admin.admins.admins.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'username',
                    'title' => app($this->model)->attributes('username'),
                    'type' => 'text',
                ],
                [
                    'name' => 'status',
                    'title' => app($this->model)->attributes('status'),
                    'type' => 'select',
                    'list' => app($this->model)->list_status,
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
     * 添加账号
     */
    public function add()
    {
        if(request()->ajax()){
            $result = app($this->model)->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.admins.update') ? route('admin.admins.update', ['id' => $result['data']['id']]) : url('admins.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        $form = [
            [
                'name' => 'username',
                'title' => app($this->model)->attributes('username'),
                'type' => 'text',
            ],
            [
                'name' => 'nickname',
                'title' => app($this->model)->attributes('nickname'),
                'type' => 'text',
            ],
            [
                'name' => 'avatar',
                'title' => app($this->model)->attributes('avatar'),
                'type' => 'file_image',
            ],
            [
                'name' => 'mobile',
                'title' => app($this->model)->attributes('mobile'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'title' => app($this->model)->attributes('email'),
                'type' => 'text',
            ],
            [
                'name' => 'password',
                'title' => app($this->model)->attributes('password'),
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
                'title' => app($this->model)->attributes('status'),
                'value' => 1,
                'type' => 'radio',
                'list' => app($this->model)->list_status,
            ],
            ['type' => 'line'],
            [
                'name' => 'role_id',
                'title' => app($this->model)->attributes('role_id'),
                'type' => 'checkbox',
                'list' => app($this->roles_model)->toListAll(),
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
            return $this->json(app($this->model)->toUpdate());
        }
        $data = app($this->model)->toInfo();
        $form = [
            [
                'name' => 'username',
                'title' => app($this->model)->attributes('username'),
                'value' => $data->username,
                'type' => 'text',
            ],
            [
                'name' => 'nickname',
                'title' => app($this->model)->attributes('nickname'),
                'value' => $data->nickname,
                'type' => 'text',
            ],
            [
                'name' => 'avatar',
                'title' => app($this->model)->attributes('avatar'),
                'value' =>  $data->avatar ?? '',
                'type' => 'file_image',
            ],
            [
                'name' => 'mobile',
                'title' => app($this->model)->attributes('mobile'),
                'value' => $data->mobile,
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'title' => app($this->model)->attributes('email'),
                'value' => $data->email,
                'type' => 'text',
            ],
            [
                'name' => 'password',
                'title' => app($this->model)->attributes('password'),
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
                'title' => app($this->model)->attributes('status'),
                'value' => $data->status,
                'type' => 'radio',
                'list' => app($this->model)->list_status,
            ],
            ['type' => 'line'],
            [
                'name' => 'role_id',
                'title' => app($this->model)->attributes('role_id'),
                'value' => app($this->admins_roles_model)->where('admin_id', $data->id)->pluck('role_id')->toArray(),
                'type' => 'checkbox',
                'list' => app($this->roles_model)->toListAll(),
            ],
            [
                'name' => 'id',
                'title' => app($this->model)->attributes('id'),
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
            return $this->json(app($this->model)->toDelete());
        }
        return $this->error();
    }

    /**
     * 恢复账号
     */
    public function restore()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toRestore());
        }
        return $this->error();
    }

    /**
     * 导出账号
     */
    public function export()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toList());
        }
        return $this->error();
    }

}
