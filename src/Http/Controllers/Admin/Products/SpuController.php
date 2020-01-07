<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/14
 * Time: 5:45 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Products;


use Z1px\App\Http\Controllers\AdminController;
use Z1px\Tool\Arr;

class SpuController extends AdminController
{

    /**
     * 产品列表
     */
    public function index()
    {
        return view('admin.products.spu.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'title',
                    'title' => app('spu_service')->attributes('title'),
                    'type' => 'text',
                ],
                [
                    'name' => 'category_tid',
                    'title' => app('spu_service')->attributes('category_tid'),
                    'type' => 'select',
                    'search' => true,
                    'list' => app('category_service')->toListAll(['level' => 1]),
                ],
                [
                    'name' => 'category_pid',
                    'title' => app('spu_service')->attributes('category_pid'),
                    'type' => 'select',
                    'search' => true,
                    'list' => request()->input('category_tid') ? app('category_service')->toListAll(['level' => 2, 'pid' => request()->input('category_tid')]) : [],
                ],
                [
                    'name' => 'category_id',
                    'title' => app('spu_service')->attributes('category_id'),
                    'type' => 'select',
                    'search' => true,
                    'multiple' => 'multiple',
                    'list' => request()->input('category_pid') ? app('category_service')->toListAll(['level' => 3, 'pid' => request()->input('category_pid')]) : [],
                ],
                [
                    'name' => 'status',
                    'title' => app('spu_service')->attributes('status'),
                    'type' => 'select',
                    'list' => app('spu_service')->list_status,
                ],
            ]))
            ->with('data', app('spu_service')->toList())
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 添加产品
     */
    public function add()
    {
        // 构造属性表单
        $buildAttributesInput = function ($category_id){
            if(empty($category_id)) return '';
            $html_attribute1 = $html_attribute2 = $html_attribute3 = "";
            $list_attributes = app('category_attributes_service')->toListAll(['category_id' => $category_id]);
            if(!empty($list_attributes)){
                foreach ($list_attributes as $key=>$value){
                    if(empty($value->attr)) continue;
                    switch ($value->type){
                        case 1:
                            $html_attribute1 .= $this->buildGroupInput([
                                'name' => "specs1[{$value->attr->id}]",
                                'title' => $value->attr->title,
                                'type' => 'select',
                                'search' => true,
                                'list' => $value->attr->specs
                            ]);
                            break;
                        case 2:
                            $html_attribute2 .= $this->buildGroupInput([
                                'name' => "specs2[{$value->attr->id}]",
                                'title' => $value->attr->title,
                                'type' => 'checkbox',
                                'list' => $value->attr->specs
                            ]);
                            break;
                        case 3:
                            $html_attribute3 .= $this->buildGroupInput([
                                'name' => "specs3[{$value->attr->id}]",
                                'title' => $value->attr->title,
                                'type' => 'text',
                            ]);
                            break;
                    }
                }
            }
            unset($list_attributes);
            $html = '';
            $html .= '<fieldset class="b-light m-b">';
            $html .= '<legend>' . (app('attributes_service')->list_type[2] ?? '销售属性') . '</legend>';
            $html .= $html_attribute2;
            $html .= '</fieldset>';
            $html .= '<fieldset class="b-light m-b">';
            $html .= '<legend>' . (app('attributes_service')->list_type[1] ?? '关键属性') . '</legend>';
            $html .= $html_attribute1;
            $html .= '</fieldset>';
            $html .= '<fieldset class="b-light m-b">';
            $html .= '<legend>' . (app('attributes_service')->list_type[3] ?? '非关键属性') . '</legend>';
            $html .= $html_attribute3;
            $html .= '</fieldset>';
            unset($html_attribute1, $html_attribute2, $html_attribute3);
            return $html;
        };
        if(request()->has('_input')){
            return $this->json([
                'data' => $buildAttributesInput(request()->input('category_id'))
            ]);
        }
        if(request()->has('_sku')){
            $category_id = request()->input('category_id');
            $specs2 = request()->input('specs2');
            if(!$category_id || empty($specs2) || !is_array($specs2)){
                return $this->json();
            }
            if(count($specs2) !== app('category_attributes_service')->where('category_id', $category_id)->where('type', 2)->count()){
                return $this->json();
            }
            unset($category_id);
            $descartes = Arr::descartes($specs2);
            $html = '';
            if(!empty($descartes)){
                $list_specs = app('specs_service')->whereIn('id', array_reduce($specs2, 'array_merge', []))->pluck('title', 'id')->toArray();
                $list_attrs = app('attributes_service')->whereIn('id', array_keys($specs2))->orderByRaw('field(id,' . implode(',', array_keys($specs2)) . ') asc')->pluck('title', 'id')->toArray();
                $html .= "<div class='form-group'>";
                $html .= "<label class='col-sm-2 control-label'>库存管理</label>";
                $html .= "<div class='col-sm-10 text-left'>";
                $html .= "<div class='panel-heading'><small class='text-danger'>注：售价不填写或者小于等于0，则不加入库存</small></div>";
                $html .= "<div class='panel panel-default'>";
                $html .= "<table class='table table-striped m-b-none'>";
                $html .= "<thead>";
                $html .= "<tr>";
                $html .= "<th>序号</th>";
                foreach ($list_attrs as $value){
                    $html .= "<th>{$value}</th>";
                }
                $html .= "<th>售价</th>";
                $html .= "<th>库存</th>";
//                $html .= "<th>产品序列号</th>";
                $html .= "</tr>";
                $html .= "</thead>";
                $html .= "<tbody>";
                foreach ($descartes as $key=>$value){
                    $html .= "<tr>";
                    $html .= "<td>" . ($key + 1) . "</td>";
                    foreach ($value as $val){
                        $html .= "<td>" . ($list_specs[$val] ?? '') . "</td>";
                    }
                    $html .= "<td><input name='sku_specs[" . implode(',', $value) . "]' type='text' value='' class='form-control'></td>";
                    $html .= "<td>--</td>";
//                    $html .= "<td>--</td>";
                    $html .= "</tr>";
                }
                $html .= "</tbody>";
                $html .= "</table>";
                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
                unset($list_specs, $list_attrs);
            }
            unset($descartes, $specs2);
            return $this->json([
                'data' => $html,
            ]);
        }
        if(request()->ajax()){
            $result = app('spu_service')->toAdd();
            if(1 === $result['code']){
                $result['url'] = app('router')->has('admin.spu.update') ? route('admin.spu.update', ['id' => $result['data']['id']]) : route('spu.update', ['id' => $result['data']['id']]);
            }
            return $this->json($result);
        }
        $form = [
            [
                'name' => 'title',
                'title' => app('spu_service')->attributes('title'),
                'type' => 'text',
            ],
            [
                'name' => 'image',
                'title' => app('spu_service')->attributes('image'),
                'type' => 'file_image',
            ],
            [
                'name' => 'images',
                'title' => app('spu_service')->attributes('images'),
                'type' => 'file_image',
                'multiple' => 'multiple',
            ],
            [
                'name' => 'brand_id',
                'title' => app('spu_service')->attributes('brand_id'),
                'type' => 'select',
                'search' => true,
                'list' => app('brands_service')->toListAll(),
            ],
            function(){
                $html = '';
                $html .= '<div class="form-group">';
                $html .= '<label class="col-sm-2 control-label">分类选择</label>';
                $html .= '<div class="col-sm-3">';
                $html .= $this->buildInput([
                    'name' => 'category_tid',
                    'title' => app('spu_service')->attributes('category_tid'),
                    'type' => 'select',
                    'search' => true,
                    'list' => app('category_service')->toListAll(['level' => 1]),
                ]);
                $html .= '</div>';
                $html .= '<div class="col-sm-3">';
                $html .= $this->buildInput([
                    'name' => 'category_pid',
                    'title' => app('spu_service')->attributes('category_pid'),
                    'type' => 'select',
                    'search' => true,
                    'list' => request()->input('category_tid') ? app('category_service')->toListAll(['level' => 2, 'pid' => request()->input('category_tid')]) : [],
                ]);
                $html .= '</div>';
                $html .= '<div class="col-sm-3">';
                $html .= $this->buildInput([
                    'name' => 'category_id',
                    'title' => app('spu_service')->attributes('category_id'),
                    'type' => 'select',
                    'search' => true,
                    'list' => request()->input('category_pid') ? app('category_service')->toListAll(['level' => 3, 'pid' => request()->input('category_pid')]) : [],
                ]);
                $html .= '</div>';
                $html .= '<div class="col-sm-1"></div>';
                $html .= '</div>';
                return $html;
            },
            $buildAttributesInput(request()->input('category_id')),
            [
                'name' => 'service_id',
                'title' => app('spu_service')->attributes('service_id'),
                'type' => 'checkbox',
                'list' => app('services_service')->toListAll(),
            ],
            [
                'name' => 'description',
                'title' => app('spu_service')->attributes('description'),
                'type' => 'textarea',
            ],
            [
                'name' => 'status',
                'title' => app('spu_service')->attributes('status'),
                'value' => 1,
                'type' => 'radio',
                'list' => app('spu_service')->list_status,
            ]
        ];
        return view('admin.products.spu.add')
            ->with('input_form', $this->buildForm($form, null, 'post', 'multipart/form-data'))
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 修改产品
     */
    public function update()
    {
        // 构造库存表单
        $buildSkuInput = function ($data, $specs2, $list_specs, $list_attrs){
            if(empty($specs2) || !is_array($specs2)){
                return '';
            }
            if(count($specs2) !== app('category_attributes_service')->where('category_id', $data->category_id)->where('type', 2)->count()){
                return '';
            }
            unset($category_id);
            $descartes = Arr::descartes($specs2);
            $html = '';
            if(!empty($descartes)){
                $html .= "<div class='form-group'>";
                $html .= "<label class='col-sm-2 control-label'>库存管理</label>";
                $html .= "<div class='col-sm-10 text-left'>";
                $html .= "<div class='panel-heading'><small class='text-danger'>注：售价不填写或者小于等于0，则不加入库存</small></div>";
                $html .= "<div class='panel panel-default'>";
                $html .= "<table class='table table-striped m-b-none'>";
                $html .= "<thead>";
                $html .= "<tr>";
                $html .= "<th>序号</th>";
                foreach ($list_attrs as $value){
                    $html .= "<th>{$value}</th>";
                }
                $html .= "<th>售价</th>";
                $html .= "<th>库存</th>";
                $html .= "<th>产品序列号</th>";
                $html .= "</tr>";
                $html .= "</thead>";
                $html .= "<tbody>";

                $list_sku = $data->sku()->get();
                $list_sku_specs = [];
                $num = 0;
                foreach ($list_sku as $key=>$value){
                    $num++;
                    $key = $value->specs()->orderBy($value->specs()->getTable() . '.id', 'asc')->pluck('spec_id')->toArray();
                    $html .= "<tr>";
                    $html .= "<td>{$num}</td>";
                    foreach ($key as $val){
                        $html .= "<td>" . ($list_specs[$val] ?? '') . "</td>";
                    }
                    $html .= "<td><input name='sku_specs[" . implode(',', $key) . "]' type='text' value='{$value->price}' class='form-control'></td>";
                    $html .= "<td>{$value->stock}</td>";
                    $html .= "<td>{$value->sn}</td>";
                    $html .= "</tr>";
                    $list_sku_specs[] = implode(',', $key);
                }
                unset($list_sku);

                foreach ($descartes as $key=>$value){
                    if(in_array(implode(',', $value), $list_sku_specs)) continue;
                    $num++;
                    $html .= "<tr>";
                    $html .= "<td>{$num}</td>";
                    foreach ($value as $val){
                        $html .= "<td>" . ($list_specs[$val] ?? '') . "</td>";
                    }
                    $html .= "<td><input name='sku_specs[" . implode(',', $value) . "]' type='text' value='' class='form-control'></td>";
                    $html .= "<td>--</td>";
                    $html .= "<td>--</td>";
                    $html .= "</tr>";
                }
                unset($list_sku_specs, $num);

                $html .= "</tbody>";
                $html .= "</table>";
                $html .= "</div>";
                $html .= "</div>";
                $html .= "</div>";
            }
            unset($descartes, $specs2, $list_specs, $list_attrs);
            return $html;
        };

        if(request()->has('_sku')){
            $data = app('spu_service')->toInfo();
            $specs2 = request()->input('specs2');
            $list_specs = app('specs_service')->whereIn('id', array_reduce($specs2, 'array_merge', []))->pluck('title', 'id')->toArray();
            $list_attrs = app('attributes_service')->whereIn('id', array_keys($specs2))->orderByRaw('field(id,' . implode(',', array_keys($specs2)) . ') asc')->pluck('title', 'id')->toArray();
            return $this->json([
                'data' => $buildSkuInput($data, $specs2, $list_specs, $list_attrs),
            ]);
        }
        if(request()->ajax()){
            return $this->json(app('spu_service')->toUpdate());
        }
        $data = app('spu_service')->toInfo();
        $form = [
            [
                'name' => 'title',
                'title' => app('spu_service')->attributes('title'),
                'value' => $data->title,
                'type' => 'text',
            ],
            [
                'name' => 'image',
                'title' => app('spu_service')->attributes('image'),
                'value' => $data->image,
                'type' => 'file_image',
            ],
            [
                'name' => 'images',
                'title' => app('spu_service')->attributes('images'),
                'type' => 'file_image',
                'multiple' => 'multiple',
                'list' => $data->images
            ],
            [
                'name' => 'brand_id',
                'title' => app('spu_service')->attributes('brand_id'),
                'value' => $data->brand_id,
                'type' => 'select',
                'search' => true,
                'list' => app('brands_service')->toListAll(),
            ],
            function() use($data){
                $html = '';
                $html .= '<div class="form-group">';
                $html .= '<label class="col-sm-2 control-label">分类选择</label>';
                $html .= '<div class="col-sm-3">';
                $html .= $this->buildInput([
                    'name' => 'category_tid',
                    'title' => app('spu_service')->attributes('category_tid'),
                    'value' => $data->category_tid,
                    'type' => 'select',
                    'search' => true,
                    'disabled' => 'disabled',
                    'list' => app('category_service')->toListAll(['level' => 1]),
                ]);
                $html .= '</div>';
                $html .= '<div class="col-sm-3">';
                $html .= $this->buildInput([
                    'name' => 'category_pid',
                    'title' => app('spu_service')->attributes('category_pid'),
                    'value' => $data->category_pid,
                    'type' => 'select',
                    'search' => true,
                    'disabled' => 'disabled',
                    'list' => app('category_service')->toListAll(['level' => 2, 'pid' => $data->category_tid]),
                ]);
                $html .= '</div>';
                $html .= '<div class="col-sm-3">';
                $html .= $this->buildInput([
                    'name' => 'category_id',
                    'title' => app('spu_service')->attributes('category_id'),
                    'value' => $data->category_id,
                    'type' => 'select',
                    'search' => true,
                    'disabled' => 'disabled',
                    'list' => app('category_service')->toListAll(['level' => 3, 'pid' => $data->category_pid]),
                ]);
                $html .= '</div>';
                $html .= '<div class="col-sm-1"></div>';
                $html .= '</div>';
                return $html;
            },
            function() use($data, $buildSkuInput){
                $html_attribute1 = $html_attribute2 = $html_attribute3 = "";
                $list_attributes = app('category_attributes_service')->toListAll(['category_id' => $data->category_id]);
                if(!empty($list_attributes)){
                    $list_pivot_specs = $data->specs()->pluck('spec_id')->toArray();
                    $specs2 = [];
                    $list_specs = [];
                    $list_attrs = [];
                    foreach ($list_attributes as $key=>$value){
                        switch ($value->type){
                            case 1:
                                $html_attribute1 .= $this->buildGroupInput([
                                    'name' => "specs1[{$value->attr->id}]",
                                    'title' => $value->attr->title,
                                    'value' => app('spu_attributes_service')->where('spu_id', $data->id)->where('attribute_id', $value->attr->id)->value('spec_id'),
                                    'type' => 'select',
                                    'search' => true,
                                    'list' => $value->attr->specs
                                ]);
                                break;
                            case 2:
                                $list_specs2 = $value->attr->specs;
                                foreach ($list_specs2 as $k=>$val){
                                    // 设置已添加的销售属性不允许再删除
                                    if(in_array($val->id, $list_pivot_specs)){
                                        $val->setAttribute('status', 2);
                                        $specs2[$value->attr->id][] = $val->id;
                                    }
                                    $list_specs[$val->id] = $val->title;
                                }
                                $list_attrs[$value->attr->id] = $value->attr->title;
                                $html_attribute2 .= $this->buildGroupInput([
                                    'name' => "specs2[{$value->attr->id}]",
                                    'title' => $value->attr->title,
                                    'value' => $list_pivot_specs,
                                    'type' => 'checkbox',
                                    'list' => $list_specs2
                                ]);
                                unset($list_specs2);
                                break;
                            case 3:
                                $html_attribute3 .= $this->buildGroupInput([
                                    'name' => "specs3[{$value->attr->id}]",
                                    'title' => $value->attr->title,
                                    'value' => app('spu_attributes_service')->where('spu_id', $data->id)->where('attribute_id', $value->attr->id)->value('title'),
                                    'type' => 'text',
                                ]);
                                break;
                        }
                    }
                    unset($list_pivot_specs);
                    $html_attribute2 .= $buildSkuInput($data, $specs2, $list_specs, $list_attrs);
                    unset($descartes, $specs2, $list_specs, $list_attrs);
                }
                unset($list_attributes);
                $html = '';
                $html .= '<fieldset class="b-light m-b">';
                $html .= '<legend>' . (app('attributes_service')->list_type[2] ?? '销售属性') . '</legend>';
                $html .= $html_attribute2;
                $html .= '</fieldset>';
                $html .= '<fieldset class="b-light m-b">';
                $html .= '<legend>' . (app('attributes_service')->list_type[1] ?? '关键属性') . '</legend>';
                $html .= $html_attribute1;
                $html .= '</fieldset>';
                $html .= '<fieldset class="b-light m-b">';
                $html .= '<legend>' . (app('attributes_service')->list_type[3] ?? '非关键属性') . '</legend>';
                $html .= $html_attribute3;
                $html .= '</fieldset>';
                unset($html_attribute1, $html_attribute2, $html_attribute3);
                return $html;
            },
            [
                'name' => 'service_id',
                'title' => app('spu_service')->attributes('service_id'),
                'value' => $data->services()->pluck('service_id')->toArray(),
                'type' => 'checkbox',
                'list' => app('services_service')->toListAll(),
            ],
            [
                'name' => 'description',
                'title' => app('spu_service')->attributes('description'),
                'value' => $data->description,
                'type' => 'textarea',
            ],
            [
                'name' => 'status',
                'title' => app('spu_service')->attributes('status'),
                'value' => $data->status,
                'type' => 'radio',
                'list' => app('spu_service')->list_status,
            ],
            [
                'name' => 'id',
                'title' => app('spu_service')->attributes('id'),
                'value' => $data->id,
                'type' => 'hidden',
            ]
        ];
        return view('admin.products.spu.update')
            ->with('input_form', $this->buildForm($form, null, 'post', 'multipart/form-data'))
            ->with('data', $data)
            ->with('list_menu', app('menu_logic')->toList());
    }

}
