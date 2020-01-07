<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 10:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;

class CategoryController extends AdminController
{

    /**
     * 分类列表
     */
    public function index()
    {
        return view('admin.products.category.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app('category_service')->attributes('title'),
                    'type' => 'text',
                ],
//                [
//                    'name' => 'tid',
//                    'title' => app('category_service')->attributes('tid'),
//                    'type' => 'select',
//                    'search' => true,
//                    'list' => app('category_service')->toListAll(['level' => 1]),
//                ],
//                [
//                    'name' => 'pid',
//                    'title' => app('category_service')->attributes('pid'),
//                    'type' => 'select',
//                    'search' => true,
//                    'list' => request()->input('tid') ? app('category_service')->toListAll(['level' => 2, 'pid' => request()->input('tid')]) : [],
//                ],
//                [
//                    'name' => 'status',
//                    'title' => app('category_service')->attributes('status'),
//                    'type' => 'select',
//                    'list' => app('category_service')->list_status,
//                ],
            ]))
//            ->with('table_form', $this->buildTable(
//                app('category_service')->toList(),
//                [
//                    'id' => 'ID',
//                    'level_name' => '级别',
//                    'top_title' => '一级分类名称',
//                    'parent_title' => '二级分类名称',
//                    'title' => '分类名称',
//                    'status' => '状态',
//                    'created_at' => '创建时间',
//                ],
//                [
//                    'table_title' => '分类列表', // table表标题
//                    'runtime' => true, // 响应时间
//                    'export' => '', // 导出地址
//                    'action' => [ // table操作
//                        'swal-update' => 'admin.category.update', // 更新地址
//                        'callback' => function($data){
//                            if(empty($data) || 'object' !== gettype($data)) return '';
//                            if(3 !== ($data->level ?? '')) return '';
//                            return "<a href='" . (app('router')->has('admin.spu.add') ? route('admin.spu.add', ['category_tid' => $data->tid ?? '', 'category_pid' => $data->pid ?? '', 'category_id' => $data->id ?? '']) : url('admin.spu.add', ['category_tid' => $data->tid ?? '', 'category_pid' => $data->pid ?? '', 'category_id' => $data->id ?? ''])) . "' class='btn-link text-info text-u-l'>新增产品</a>";
//                        }
//                    ],
//                    'pager' => 'pager', // 分页模版
//                    'total' => [], // 数据总揽
//                    'tr_title' => 'title', // table表行标题，data字段
//                ]
//            ))
            ->with('data',app('category_service')->toListAll(['level' => 1]))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加分类
     */
    public function add()
    {
        $attributes = function (){
            return implode('', [
                $this->buildGroupInput(['type' => 'line']),
                $this->buildGroupInput([
                    'name' => 'attribute_id',
                    'title' => app('attributes_service')->list_type[1] ?? '关键属性',
                    'type' => 'checkbox',
                    'list' => app('attributes_service')->toListAll(['type' => 1])
                ], 3),
                $this->buildGroupInput([
                    'name' => 'attribute_id',
                    'title' => app('attributes_service')->list_type[2] ?? '销售属性',
                    'type' => 'checkbox',
                    'list' => app('attributes_service')->toListAll(['type' => 2])
                ], 3),
                $this->buildGroupInput([
                    'name' => 'attribute_id',
                    'title' => app('attributes_service')->list_type[3] ?? '非关键属性',
                    'type' => 'checkbox',
                    'list' => app('attributes_service')->toListAll(['type' => 3])
                ], 3)
            ]);
        };
        if(request()->has('_form')){
            $form = [
                function() {
                    $level = request()->input('level', 0);
                    return $level >= 1 ? [
                        'name' => 'tid',
                        'title' => app('category_service')->attributes('tid'),
                        'type' => 'select',
                        'search' => true,
                        'list' => app('category_service')->toListAll(['level' => 1])
                    ] : '';
                },
                function() {
                    $level = request()->input('level', 0);
                    return $level >= 2 ? [
                        'name' => 'pid',
                        'title' => app('category_service')->attributes('pid'),
                        'type' => 'select',
                        'search' => true,
                        'list' => request()->input('tid') ? app('category_service')->toListAll(['level' => 2, 'pid' => request()->input('tid')]) : [],
                    ] : '';
                },
                [
                    'name' => 'title',
                    'title' => app('category_service')->attributes('title'),
                    'type' => 'text',
                ],
                function() use($attributes){
                    return request()->input('pid') ? $attributes() : '';
                },
//                [
//                    'name' => 'status',
//                    'title' => app('category_service')->attributes('status'),
//                    'value' => 1,
//                    'type' => 'radio',
//                    'list' => app('category_service')->list_status,
//                ]
            ];
            return $this->json([
                'title' => '添加分类',
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'title' => '分类名称不能为空',
                ]
            ]);
        }
        if(request()->has('_input')){
            return $this->json([
                'code' => 1,
                'message' => 'data normal！',
                'data' => $attributes(),
            ]);
        }
        if(request()->ajax()){
            $result = app('category_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.category.update') ? route('admin.category.update', ['id' => $result['data']['id']]) : route('category.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        return $this->error();
    }

    /**
     * 修改分类
     */
    public function update()
    {
        if(request()->has('_form')){
            $data = app('category_service')->toInfo();
            $form = [
                [
                    'name' => 'tid',
                    'title' => app('category_service')->attributes('tid'),
                    'value' => $data->tid,
                    'type' => 'select',
                    'search' => true,
                    'disabled' => 'disabled',
                    'list' => app('category_service')->toListAll(['level' => 1])
                ],
                [
                    'name' => 'pid',
                    'title' => app('category_service')->attributes('pid'),
                    'value' => $data->pid,
                    'type' => 'select',
                    'search' => true,
                    'disabled' => 'disabled',
                    'list' => app('category_service')->toListAll(['level' => 2, 'pid' => $data->tid])
                ],
                [
                    'name' => 'title',
                    'title' => app('category_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('category_service')->attributes('status'),
//                    'value' => $data->status,
//                    'type' => 'radio',
//                    'list' => app('category_service')->list_status,
//                ],
            ];
            if(3 === $data->level){
                $list_attributes = $data->attrs()->pluck('attribute_id')->toArray();
                array_push($form,
                    ['type' => 'line'],
                    [
                        'name' => 'attribute_id',
                        'title' => app('attributes_service')->list_type[1] ?? '关键属性',
                        'value' => $list_attributes,
                        'type' => 'checkbox',
                        'list' => app('attributes_service')->toListAll(['type' => 1])
                    ],
                    [
                        'name' => 'attribute_id',
                        'title' => app('attributes_service')->list_type[2] ?? '销售属性',
                        'value' => $list_attributes,
                        'type' => 'checkbox',
                        'disabled' => app('category_service')->checkRelations($data->id),
                        'list' => app('attributes_service')->toListAll(['type' => 2])
                    ],
                    [
                        'name' => 'attribute_id',
                        'title' => app('attributes_service')->list_type[3] ?? '非关键属性',
                        'value' => $list_attributes,
                        'type' => 'checkbox',
                        'list' => app('attributes_service')->toListAll(['type' => 3])
                    ]
                );
                unset($list_attributes);
            }
            array_push($form, [
                'name' => 'id',
                'title' => app('category_service')->attributes('id'),
                'value' => $data->id,
                'type' => 'hidden',
            ]);
            return $this->json([
                'data' => $data,
                'title' => "修改<font class='text-danger'>【{$data->title}】</font>",
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'id' => '参数错误',
                    'title' => '分类名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            return $this->json(app('category_service')->toUpdate());
        }
        return $this->error();
    }

    /**
     * 顶级分类
     */
    public function all()
    {
        if(request()->ajax()) {
            return $this->json(['data' => app('category_service')->toListAll(request()->input())]);
        }
        return $this->error();
    }

}
