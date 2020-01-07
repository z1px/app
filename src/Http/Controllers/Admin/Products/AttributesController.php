<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 10:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;

class AttributesController extends AdminController
{

    /**
     * 属性列表
     */
    public function index()
    {
        return view('admin.products.attributes.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app('attributes_service')->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'attributes_group_id',
                    'title' => app('attributes_service')->attributes('attributes_group_id'),
                    'type' => 'select',
                    'search' => true,
                    'multiple' => 'multiple',
                    'list' => app('attributes_group_service')->toListAll(),
                ],
                [
                    'name' => 'type',
                    'title' => app('attributes_service')->attributes('type'),
                    'type' => 'select',
                    'multiple' => 'multiple',
                    'list' => app('attributes_service')->list_type,
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('attributes_service')->attributes('status'),
//                    'type' => 'select',
//                    'list' => app('attributes_service')->list_status,
//                ],
            ]))
            ->with('table_form', $this->buildTable(
                app('attributes_service')->toList(),
                [
                    'id' => 'ID',
                    'attributes_group_name' => '属性分组',
                    'title' => '属性名称',
                    'type_name' => '属性类型',
//                    'status' => '状态',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '属性列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [ // table操作
                        'swal-update' => 'admin.attributes.update', // 更新地址（弹窗模式）
                        1 => [
                            'title' => '新增规格',
                            'url' => 'admin.specs.add',
                            'class' => 'text-info',
                            'type' => 'swal-add',
                            'params' => [
                                'attribute_id' => 'id'
                            ]
                        ],
                        2 => [
                            'title' => '规格列表',
                            'url' => 'admin.specs',
                            'class' => 'text-primary',
                            'params' => [
                                'attribute_id' => 'id'
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
     * 添加属性
     */
    public function add()
    {
        if(request()->has('_form')){
            $form = [
                [
                    'name' => 'attributes_group_id',
                    'title' => app('attributes_service')->attributes('attributes_group_id'),
                    'type' => 'select',
                    'search' => true,
                    'list' => app('attributes_group_service')->toListAll(),
                ],
                [
                    'name' => 'title',
                    'title' => app('attributes_service')->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'type',
                    'title' => app('attributes_service')->attributes('type'),
                    'value' => 1,
                    'type' => 'radio',
                    'list' => app('attributes_service')->list_type,
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('attributes_service')->attributes('status'),
//                    'value' => 1,
//                    'type' => 'radio',
//                    'list' => app('attributes_service')->list_status,
//                ]
            ];
            return $this->json([
                'title' => '添加属性',
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'attributes_group_id' => '属性分组不能为空',
                    'title' => '属性名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app('attributes_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.attributes.update') ? route('admin.attributes.update', ['id' => $result['data']['id']]) : route('attributes.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        return $this->error();
    }

    /**
     * 修改属性
     */
    public function update()
    {
        if(request()->has('_form')){
            $data = app('attributes_service')->toInfo();
            $form = [
                [
                    'name' => 'attributes_group_id',
                    'title' => app('attributes_service')->attributes('attributes_group_id'),
                    'value' => $data->attributes_group_id,
                    'type' => 'select',
                    'search' => true,
                    'list' => app('attributes_group_service')->toListAll(),
                ],
                [
                    'name' => 'title',
                    'title' => app('attributes_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                ],
                [
                    'name' => 'type',
                    'title' => app('attributes_service')->attributes('type'),
                    'value' => $data->type,
                    'type' => 'radio',
                    'disabled' => app('attributes_service')->checkRelations($data->id),
                    'list' => app('attributes_service')->list_type,
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('attributes_service')->attributes('status'),
//                    'value' => $data->status,
//                    'type' => 'radio',
//                    'list' => app('attributes_service')->list_status,
//                ],
                [
                    'name' => 'id',
                    'title' => app('attributes_service')->attributes('id'),
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
                    'attributes_group_id' => '属性分组不能为空',
                    'title' => '属性名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            return $this->json(app('attributes_service')->toUpdate());
        }
        return $this->error();
    }

}
