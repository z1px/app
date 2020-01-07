<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 10:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;

class SpecsController extends AdminController
{

    /**
     * 规格列表
     */
    public function index()
    {
        return view('admin.products.specs.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app('specs_service')->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'attribute_id',
                    'title' => app('specs_service')->attributes('attribute_id'),
                    'type' => 'select',
                    'search' => true,
                    'multiple' => 'multiple',
                    'optgroup' => 'type_name',
                    'list' => app('attributes_service')->toListAll(),
                ],
//                [
//                    'name' => 'status',
//                    'title' => '状态',
//                    'type' => 'select',
//                    'list' => app('specs_service')->list_status,
//                ],
            ]))
            ->with('table_form', $this->buildTable(
                app('specs_service')->toList(),
                [
                    'id' => 'ID',
                    'attribute_name' => '属性名称',
                    'title' => '规格名称',
                    'logo' => '规格LOGO',
//                    'status' => '状态',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '规格列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [ // table操作
                        'swal-update' => 'admin.specs.update', // 更新地址（弹窗模式）
                    ],
                    'pager' => 'pager', // 分页模版
                    'total' => [], // 数据总揽
                    'tr_title' => 'title', // table表行标题，data字段
                ]
            ))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加规格
     */
    public function add()
    {
        if(request()->has('_form')){
            $form = [
                [
                    'name' => 'attribute_id',
                    'title' => app('specs_service')->attributes('attribute_id'),
                    'type' => 'select',
                    'search' => true,
                    'optgroup' => 'type_name',
                    'list' => app('attributes_service')->toListAll(),
                ],
                [
                    'name' => 'title',
                    'title' => app('specs_service')->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'logo',
                    'title' => app('specs_service')->attributes('logo'),
                    'type' => 'file_image',
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('specs_service')->attributes('status'),
//                    'value' => 1,
//                    'type' => 'radio',
//                    'list' => app('specs_service')->list_status,
//                ]
            ];
            return $this->json([
                'title' => '添加规格',
                'html' => $this->buildSwalForm($form, null, 'post', 'multipart/form-data'),
                'validator' => [
                    'title' => '规格名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app('specs_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.specs.update') ? route('admin.specs.update', ['id' => $result['data']['id']]) : route('specs.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        return $this->error();
    }

    /**
     * 修改规格
     */
    public function update()
    {
        if(request()->has('_form')){
            $data = app('specs_service')->toInfo();
            $form = [
                [
                    'name' => 'attribute_id',
                    'title' => app('specs_service')->attributes('attribute_id'),
                    'value' => $data->attribute_id,
                    'type' => 'select',
                    'search' => true,
                    'disabled' => 'disabled',
                    'optgroup' => 'type_name',
                    'list' => app('attributes_service')->toListAll(),
                ],
                [
                    'name' => 'title',
                    'title' => app('specs_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                ],
                [
                    'name' => 'logo',
                    'title' => app('specs_service')->attributes('logo'),
                    'value' => $data->logo,
                    'type' => 'file_image',
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('specs_service')->attributes('status'),
//                    'value' => $data->status,
//                    'type' => 'radio',
//                    'list' => app('specs_service')->list_status,
//                ],
                [
                    'name' => 'id',
                    'title' => app('specs_service')->attributes('id'),
                    'value' => $data->id,
                    'type' => 'hidden',
                ],
            ];
            return $this->json([
                'data' => $data,
                'title' => "修改<font class='text-danger'>【{$data->title}】</font>",
                'html' => $this->buildSwalForm($form, null, 'post', 'multipart/form-data'),
                'validator' => [
                    'id' => '参数错误',
                    'title' => '规格名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            return $this->json(app('specs_service')->toUpdate());
        }
        return $this->error();
    }

}
