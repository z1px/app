<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 10:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;

class ServicesController extends AdminController
{

    /**
     * 服务列表
     */
    public function index()
    {
        return view('admin.products.services.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app('services_service')->attributes('title'),
                    'type' => 'text',
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('services_service')->attributes('status'),
//                    'type' => 'select',
//                    'list' => app('services_service')->list_status,
//                ],
            ]))
            ->with('table_form', $this->buildTable(
                app('services_service')->toList(),
                [
                    'id' => 'ID',
                    'logo' => '服务LOGO',
                    'title' => '服务名称',
                    'description' => '服务描述',
//                    'status' => '状态',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '服务列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [ // table操作
                        'swal-update' => 'admin.services.update', // 更新地址（弹窗模式）
                    ],
                    'pager' => 'pager', // 分页模版
                    'total' => [], // 数据总揽
                    'tr_title' => 'title', // table表行标题，data字段
                ]
            ))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加服务
     */
    public function add()
    {
        if(request()->has('_form')){
            $form = [
                [
                    'name' => 'title',
                    'title' => app('services_service')->attributes('title'),
                    'type' => 'text',
                ],
//                [
//                    'name' => 'status',
//                    'title' => app('services_service')->attributes('status'),
//                    'value' => 1,
//                    'type' => 'radio',
//                    'list' => app('services_service')->list_status,
//                ],
                [
                    'name' => 'logo',
                    'title' => app('brands_service')->attributes('logo'),
                    'type' => 'file_image',
                ],
                [
                    'name' => 'description',
                    'title' => app('brands_service')->attributes('description'),
                    'type' => 'textarea',
                ],
            ];
            return $this->json([
                'title' => '添加服务',
                'html' => $this->buildSwalForm($form, null, 'post', 'multipart/form-data'),
                'validator' => [
                    'title' => '服务名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app('services_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.services.update') ? route('admin.services.update', ['id' => $result['data']['id']]) : route('services.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        return $this->error();
    }

    /**
     * 修改服务
     */
    public function update()
    {
        if(request()->has('_form')){
            $data = app('services_service')->toInfo();
            $form = [
                [
                    'name' => 'title',
                    'title' => app('services_service')->attributes('title'),
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
//                [
//                    'name' => 'status',
//                    'title' => app('services_service')->attributes('status'),
//                    'value' => $data->status,
//                    'type' => 'radio',
//                    'list' => app('services_service')->list_status,
//                ],
                [
                    'name' => 'id',
                    'title' => app('services_service')->attributes('id'),
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
                    'title' => '服务名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            return $this->json(app('services_service')->toUpdate());
        }
        return $this->error();
    }

}
