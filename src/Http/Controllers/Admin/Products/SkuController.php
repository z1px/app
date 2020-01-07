<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/14
 * Time: 5:46 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;

class SkuController extends AdminController
{

    /**
     * 产品库存列表
     */
    public function index()
    {
        $data = app('spu_specs_service')
            ->where('spu_id', request()->input('spu_id'))
            ->get();
        $attributes = [];
        if(!empty($data)){
            foreach ($data as $key=>$value){
                if(isset($attributes[$value->attribute_id])) continue;
                $attributes[$value->attribute_id] = $value;
            }
        }
        unset($data);
        return view('admin.products.sku.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'status',
                    'title' => app('sku_service')->attributes('status'),
                    'type' => 'select',
                    'list' => app('sku_service')->list_status,
                ],
                [
                    'name' => 'spu_id',
                    'title' => app('stock_service')->attributes('spu_id'),
                    'type' => 'hidden',
                ],
            ]))
            ->with('table_form', $this->buildTable(
                app('sku_service')->toList(),
                [
                    'id' => 'ID',
                    'sn' => '产品序列号',
                    'title' => '产品库存名称',
                    'callback' => function($data) use($attributes){
                        $html = "";
                        if(!empty($attributes)){
                            if('object' === gettype($data)){
                                $specs = $data->specs()->orderByRaw('field(pivot_attribute_id,' . implode(',', array_keys($attributes)) . ') asc')->get();
                                if(!empty($specs)){
                                    foreach ($specs as $key=>$value){
                                        $html .= "<td>{$value->title}</td>";
                                    }
                                }
                            }else{
                                foreach ($attributes as $key=>$value){
                                    $html .= "<th>{$value->attr->title}</th>";
                                }
                            }
                        }
                        return $html;
                    },
                    'price' => '售价',
                    'stock' => '库存',
                    'status' => '状态',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '<span class="text-danger">【' . app('spu_service')->where('id', request()->input('spu_id'))->value('title') . '】</span>库存列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [ // table操作
                        'swal-update' => 'admin.sku.update', // 更新地址（弹窗模式）
                        'callback' => function($data){
                            $html = "";
                            if(empty($data) || 'object' !== gettype($data)) return $html;
                            $html .= "<a href='" . (app('router')->has('admin.sku.in') ? route('admin.sku.in', ['id' => $data->id]) : url('sku.in', ['id' => $data->id])) . "' class='btn-link text-info text-u-l swal-add'>进货</a>";
                            $html .= "<a href='" . (app('router')->has('admin.sku.out') ? route('admin.sku.out', ['id' => $data->id]) : url('sku.out', ['id' => $data->id])) . "' class='btn-link text-danger text-u-l swal-add'>退货</a>";
                            $html .= "<a href='" . (app('router')->has('admin.stock') ? route('admin.stock', ['sku_id' => $data->id]) : url('stock', ['sku_id' => $data->id])) . "' class='btn-link text-dark text-u-l'>进销列表</a>";
                            return $html;
                        }
                    ],
                    'pager' => 'pager', // 分页模版
                    'total' => [], // 数据总揽
                    'tr_title' => 'title', // table表行标题，data字段
                ]
            ))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 修改产品库存
     */
    public function update()
    {
        if(request()->has('_form')){
            $data = app('sku_service')->toInfo();
            $form = [
                [
                    'name' => 'title',
                    'title' => app('sku_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                ],
                function() use($data){
                    $html = '';
                    $html .= '<fieldset class="b-light m-b">';
                    $html .= '<legend>' . (app('attributes_service')->list_type[2] ?? '销售属性') . '</legend>';
                    foreach ($data->specs()->get() as $key=>$value){
                        $html .= '<div class="form-group">';
                        $html .= '<label class="col-sm-3 control-label">' . $value->attr->title . '</label>';
                        $html .= '<div class="col-sm-4">';
                        $html .= $this->buildInput([
                            'name' => 'specs[' . $value->id . ']',
                            'title' => $value->title,
                            'value' => $value->pivot->title,
                            'type' => 'text',
                        ]);
                        $html .= '</div>';
                        $html .= '<div class="col-sm-5 upload-image">';
                        $html .= $this->buildInput([
                            'name' => 'logo[' . $value->id . ']',
                            'title' => $value->title,
                            'value' => $value->file_to_image($value->pivot->file_id),
                            'type' => 'file_image',
                        ]);
                        $html .= '</div>';
                        $html .= '</div>';
                    }
                    $html .= '</fieldset>';
                    return $html;
                },
                [
                    'name' => 'price',
                    'title' => app('sku_service')->attributes('price'),
                    'value' => $data->price,
                    'type' => 'text',
                ],
                [
                    'name' => 'status',
                    'title' => app('sku_service')->attributes('status'),
                    'value' => $data->status,
                    'type' => 'radio',
                    'list' => app('sku_service')->list_status,
                ],
                [
                    'name' => 'id',
                    'title' => app('sku_service')->attributes('id'),
                    'value' => $data->id,
                    'type' => 'hidden',
                ]
            ];
            return $this->json([
                'data' => $data,
                'title' => "修改<font class='text-danger'>【{$data->sn}】</font>",
                'html' => $this->buildSwalForm($form, null, 'post', 'multipart/form-data'),
                'validator' => [
                    'id' => '参数错误',
                    'title' => '规格名称不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            return $this->json(app('sku_service')->toUpdate());
        }
        return $this->error();
    }

    public function in()
    {
        if(request()->has('_form')){
            $data = app('sku_service')->toInfo();
            $form = [
                [
                    'name' => 'title',
                    'title' => app('sku_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                    'disabled' => 'disabled',
                ],
                [
                    'name' => 'sn',
                    'title' => app('sku_service')->attributes('sn'),
                    'value' => $data->sn,
                    'type' => 'text',
                    'disabled' => 'disabled',
                ],
                [
                    'name' => 'price',
                    'title' => '进货价格',
                    'type' => 'text',
                ],
                [
                    'name' => 'stock',
                    'title' => app('stock_service')->attributes('stock'),
                    'type' => 'text',
                ],
                [
                    'name' => 'remark',
                    'title' => app('stock_service')->attributes('remark'),
                    'type' => 'textarea',
                ],
                [
                    'name' => 'id',
                    'title' => app('sku_service')->attributes('id'),
                    'value' => $data->id,
                    'type' => 'hidden',
                ],
            ];
            return $this->json([
                'title' => '进货',
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'id' => '参数错误',
                    'price' => '进货价格不能为空',
                    'stock' => '进货数量不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app('sku_service')->toIn();
            return $this->json($result);
        }
        return $this->error();
    }

    public function out()
    {
        if(request()->has('_form')){
            $data = app('sku_service')->toInfo();
            $form = [
                [
                    'name' => 'title',
                    'title' => app('sku_service')->attributes('title'),
                    'value' => $data->title,
                    'type' => 'text',
                    'disabled' => 'disabled',
                ],
                [
                    'name' => 'sn',
                    'title' => app('sku_service')->attributes('sn'),
                    'value' => $data->sn,
                    'type' => 'text',
                    'disabled' => 'disabled',
                ],
                [
                    'name' => 'price',
                    'title' => '退货价格',
                    'type' => 'text',
                ],
                [
                    'name' => 'stock',
                    'title' => app('stock_service')->attributes('stock'),
                    'value' => $data->stock,
                    'type' => 'text',
                ],
                [
                    'name' => 'remark',
                    'title' => app('stock_service')->attributes('remark'),
                    'type' => 'textarea',
                ],
                [
                    'name' => 'id',
                    'title' => app('sku_service')->attributes('id'),
                    'value' => $data->id,
                    'type' => 'hidden',
                ],
            ];
            return $this->json([
                'title' => '<span class="text-danger">退货</span>',
                'html' => $this->buildSwalForm($form),
                'validator' => [
                    'id' => '参数错误',
                    'price' => '退货价格不能为空',
                    'stock' => '退货数量不能为空',
                ]
            ]);
        }
        if(request()->ajax()){
            $result = app('sku_service')->toOut();
            return $this->json($result);
        }
        return $this->error();
    }

}
