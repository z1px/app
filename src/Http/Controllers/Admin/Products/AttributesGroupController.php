<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 10:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;

class AttributesGroupController extends AdminController
{

    /**
     * 属性分组列表
     */
    public function index()
    {
        return view('admin.products.attributes.group.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app('attributes_group_service')->attributes('title'),
                    'type' => 'text',
                ],
            ]))
            ->with('table_form', $this->buildTable(
                app('attributes_group_service')->toList(),
                [
                    'id' => 'ID',
                    'title' => '属性分组名称',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '属性分组列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [ // table操作
                        'swal-update' => 'admin.attributes.group.update', // 更新地址（弹窗模式）
                        1 => [
                            'title' => '新增属性',
                            'url' => 'admin.attributes.add',
                            'class' => 'text-info',
                            'type' => 'swal-add',
                            'params' => [
                                'attributes_group_id' => 'id'
                            ]
                        ],
                    ],
                    'pager' => 'pager', // 分页模版
                    'total' => [], // 数据总揽
                    'tr_title' => 'title', // table表行标题，data字段
                ]
            ))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加属性分组
     */
    public function add()
    {
        if(request()->has('_form')){
            $form = [
                [
                    'name' => 'title',
                    'title' => app('attributes_group_service')->attributes('title'),
                    'type' => 'text',
                ],
            ];
            return $this->json([
                'title' => '添加属性分组',
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'title' => '属性分组名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app('attributes_group_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.attributes.group.update') ? route('admin.attributes.group.update', ['id' => $result['data']['id']]) : route('attributes.group.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        return $this->error();
    }

    /**
     * 修改属性分组
     */
    public function update()
    {
        if(request()->has('_form')){
            $data = app('attributes_group_service')->toInfo();
            $form = [
                [
                    'name' => 'title',
                    'title' => app('attributes_group_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                ],
                [
                    'name' => 'id',
                    'title' => app('attributes_group_service')->attributes('id'),
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
                    'title' => '属性分组名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            return $this->json(app('attributes_group_service')->toUpdate());
        }
        return $this->error();
    }

}
