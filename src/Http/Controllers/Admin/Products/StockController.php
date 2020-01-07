<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/22
 * Time: 9:01 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;

class StockController extends AdminController
{

    /**
     * 产品库存列表
     */
    public function index()
    {
        request()->offsetSet('spu_id', app('sku_service')->where('id', request()->input('sku_id'))->value('spu_id'));
        return view('admin.products.stock.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'type',
                    'title' => app('stock_service')->attributes('type'),
                    'type' => 'select',
                    'list' => app('stock_service')->list_type,
                ],
                [
                    'name' => 'sku_id',
                    'title' => app('stock_service')->attributes('sku_id'),
                    'type' => 'hidden',
                ],
            ]))
            ->with('table_form', $this->buildTable(
                app('stock_service')->toList(),
                [
                    'id' => 'ID',
                    'sn' => '产品序列号',
                    'title' => '产品库存名称',
                    'price' => '进销价格',
                    'stock' => '进销数量',
                    'type_name' => '进销类型',
                    'remark' => '备注',
                    'created_at' => '创建时间',
                ],
                [
                    'table_title' => '库存进销列表', // table表标题
                    'runtime' => true, // 响应时间
                    'export' => '', // 导出地址
                    'action' => [],
                    'pager' => 'pager', // 分页模版
                    'total' => [], // 数据总揽
                    'tr_title' => 'title', // table表行标题，data字段
                ]
            ))
            ->with('list_menu', app('menu_logic')->toList());
    }

}
