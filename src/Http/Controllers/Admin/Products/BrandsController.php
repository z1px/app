<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 10:12 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;

class BrandsController extends AdminController
{

    /**
     * 品牌列表
     */
    public function index()
    {
        return view('admin.products.brands.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app('brands_service')->attributes('title'),
                    'type' => 'text',
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('brands_service')->attributes('status'),
//                    'type' => 'select',
//                    'list' => app('brands_service')->list_status,
//                ],
            ]))
            ->with('table_form', $this->buildTable(
                app('brands_service')->toList(),
                [
                    'id' => 'ID',
                    'logo' => '品牌LOGO',
                    'title' => '品牌名称',
                    'pinyin' => '品牌拼音',
                    'description' => '品牌描述',
//                    'status' => '状态',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '品牌列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [ // table操作
                        'swal-update' => 'admin.brands.update', // 更新地址（弹窗模式）
                    ],
                    'pager' => 'pager', // 分页模版
                    'total' => [], // 数据总揽
                    'tr_title' => 'title', // table表行标题，data字段
                ]
            ))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加品牌
     */
    public function add()
    {
        if(request()->has('_form')){
            $form = [
                [
                    'name' => 'title',
                    'title' => app('brands_service')->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'logo',
                    'title' => app('specs_service')->attributes('logo'),
                    'type' => 'file_image',
                ],
                [
                    'name' => 'description',
                    'title' => app('brands_service')->attributes('description'),
                    'type' => 'textarea',
                ],
                [
                    'name' => 'website',
                    'title' => app('brands_service')->attributes('website'),
                    'type' => 'text',
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('brands_service')->attributes('status'),
//                    'value' => 1,
//                    'type' => 'radio',
//                    'list' => app('brands_service')->list_status,
//                ]
            ];
            return $this->json([
                'title' => '添加品牌',
                'html' => $this->buildSwalForm($form, null, 'post', 'multipart/form-data'),
                'validator' => [
                    'title' => '品牌名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app('brands_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.brands.update') ? route('admin.brands.update', ['id' => $result['data']['id']]) : route('brands.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        return $this->error();
    }

    /**
     * 修改品牌
     */
    public function update()
    {
        if(request()->has('_form')){
            $data = app('brands_service')->toInfo();
            $form = [
                [
                    'name' => 'title',
                    'title' => app('brands_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                ],
                [
                    'name' => 'logo',
                    'title' => app('brands_service')->attributes('logo'),
                    'value' => $data->logo,
                    'type' => 'file_image',
                ],
                [
                    'name' => 'description',
                    'title' => app('brands_service')->attributes('description'),
                    'value' => $data->description,
                    'type' => 'textarea',
                ],
                [
                    'name' => 'website',
                    'title' => app('brands_service')->attributes('website'),
                    'value' => $data->website,
                    'type' => 'text',
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('brands_service')->attributes('status'),
//                    'value' => $data->status,
//                    'type' => 'radio',
//                    'list' => app('brands_service')->list_status,
//                ],
                [
                    'name' => 'id',
                    'title' => app('brands_service')->attributes('id'),
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
            return $this->json(app('brands_service')->toUpdate());
        }
        return $this->error();
    }

}
