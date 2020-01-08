<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/24
 * Time: 9:33 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Admins;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\Admins\RolesService;

class RolesController extends Controller
{

    private $model = RolesService::class;

    /**
     * 角色列表
     */
    public function index()
    {
        return view('admin.admins.roles.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app($this->model)->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'status',
                    'title' => app($this->model)->attributes('status'),
                    'type' => 'select',
                    'list' => app($this->model)->list_status,
                ]
            ]))
            ->with('table_form', $this->buildTable(
                app($this->model)->toList(),
                [
                    'id' => 'ID',
                    'title' => '角色名称',
                    'status' => '状态',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '角色列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [ // table操作
                        'swal-update' => 'admin.roles.update', // 更新地址（弹窗模式）
                        'swal-delete' => 'admin.roles.delete', // 删除地址（弹窗模式）
                    ],
                    'pager' => 'pager', // 分页模版
                    'total' => [], // 数据总揽
                    'tr_title' => 'title', // table表行标题，data字段
                ]
            ))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加角色
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
                    'name' => 'status',
                    'title' => app($this->model)->attributes('status'),
                    'value' => 1,
                    'type' => 'radio',
                    'list' => app($this->model)->list_status,
                ]
            ];
            return $this->json([
                'title' => '添加角色',
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'title' => '角色名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app($this->model)->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.roles.update') ? route('admin.roles.update', ['id' => $result['data']['id']]) : url('roles.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        $form = [
            [
                'name' => 'title',
                'title' => app($this->model)->attributes('title'),
                'type' => 'text',
            ],
            [
                'name' => 'status',
                'title' => app($this->model)->attributes('status'),
                'value' => 1,
                'type' => 'radio',
                'list' => app($this->model)->list_status,
            ],
            ['type' => 'line'],
            '<div class="form-group">' .
            '<label class="col-sm-2 control-label">权限</label>' .
            '<div class="col-sm-10">' .
            '<ul id="ztree" class="ztree"></ul>' .
            '</div>' .
            '</div>',
        ];
        return view('admin.admins.roles.add')
            ->with('input_form', $this->buildForm($form, 'admin.roles.add'))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 修改角色
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
                    'title' => '角色名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            return $this->json(app($this->model)->toUpdate());
        }
        $data = app($this->model)->toInfo();
        $form = [
            [
                'name' => 'title',
                'title' => app($this->model)->attributes('title'),
                'value' => $data->title,
                'type' => 'text',
            ],
            [
                'name' => 'status',
                'title' => app($this->model)->attributes('status'),
                'value' => $data->status,
                'type' => 'radio',
                'list' => app($this->model)->list_status,
            ],
            ['type' => 'line'],
            '<div class="form-group">' .
            '<label class="col-sm-2 control-label">权限</label>' .
            '<div class="col-sm-10">' .
            '<ul id="ztree" class="ztree"></ul>' .
            '</div>' .
            '</div>',
            [
                'name' => 'id',
                'title' => app($this->model)->attributes('id'),
                'value' => $data->id,
                'type' => 'hidden',
            ],
        ];
        return view('admin.admins.roles.update')
            ->with('input_form', $this->buildForm($form))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 删除角色
     */
    public function delete()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toDelete());
        }
        return $this->error();
    }

}
