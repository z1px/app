<?php

use Illuminate\Database\Seeder;
use Z1px\App\Models\Admins\PermissionsModel;

class PermissionsTableSeeder extends Seeder
{

    private $permissions_model = PermissionsModel::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        request()->offsetSet('command',  "console: php artisan {$this->command->getName()}");
        # 添加默认权限
        $data = app($this->permissions_model)->create([
                'title' => '管理员管理',
                'route_name' => 'admin.admins'
            ]);
        array_map(function ($data){
            app($this->permissions_model)->create($data);
        }, [
            [
                'title' => '管理员列表',
                'route_name' => 'admin.admins.list',
                'pid' => $data->id
            ],
            [
                'title' => '添加管理员',
                'route_name' => 'admin.admins.add',
                'pid' => $data->id
            ],
            [
                'title' => '修改管理员',
                'route_name' => 'admin.admins.update',
                'pid' => $data->id
            ],
            [
                'title' => '获取管理员角色',
                'route_name' => 'admin.admins.getRoles',
                'pid' => $data->id
            ],
            [
                'title' => '设置管理员角色',
                'route_name' => 'admin.admins.setRoles',
                'pid' => $data->id
            ]
        ]);

        $data = app($this->permissions_model)->create([
                'title' => '角色管理',
                'route_name' => 'admin.roles'
            ]);
        array_map(function ($data){
            app($this->permissions_model)->create($data);
        }, [
            [
                'title' => '所有角色',
                'route_name' => 'admin.roles.all',
                'pid' => $data->id
            ],
            [
                'title' => '角色列表',
                'route_name' => 'admin.roles.list',
                'pid' => $data->id
            ],
            [
                'title' => '添加角色',
                'route_name' => 'admin.roles.add',
                'pid' => $data->id
            ],
            [
                'title' => '修改角色',
                'route_name' => 'admin.roles.update',
                'pid' => $data->id
            ],
            [
                'title' => '删除角色',
                'route_name' => 'admin.roles.delete',
                'pid' => $data->id
            ],
            [
                'title' => '获取角色权限',
                'route_name' => 'admin.roles.getPermissions',
                'pid' => $data->id
            ],
            [
                'title' => '设置角色权限',
                'route_name' => 'admin.roles.setPermissions',
                'pid' => $data->id
            ]
        ]);

        $data = app($this->permissions_model)->create([
                'title' => '权限管理',
                'route_name' => 'admin.permissions'
            ]);
        array_map(function ($data){
            app($this->permissions_model)->create($data);
        }, [
            [
                'title' => '所有权限',
                'route_name' => 'admin.permissions.all',
                'pid' => $data->id
            ],
            [
                'title' => '权限列表',
                'route_name' => 'admin.permissions.list',
                'pid' => $data->id
            ],
            [
                'title' => '添加权限',
                'route_name' => 'admin.permissions.add',
                'pid' => $data->id
            ],
            [
                'title' => '修改权限',
                'route_name' => 'admin.permissions.update',
                'pid' => $data->id
            ],
            [
                'title' => '删除权限',
                'route_name' => 'admin.permissions.delete',
                'pid' => $data->id
            ]
        ]);
    }
}
