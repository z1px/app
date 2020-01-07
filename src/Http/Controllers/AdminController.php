<?php

namespace Z1px\App\Http\Controllers;


class AdminController extends Controller
{

    /**
     * 构造搜索form表单
     * @param array $data
     * @param null $route_name
     * @param string $method
     * @return string
     */
    protected function buildSearchForm(array $data, $route_name=null, $method='get', $enctype='application/x-www-form-urlencoded')
    {
        if(is_null($route_name)) $route_name = request()->route()->getName();
        $html = "";
        $html .= "<form action='" . (app('router')->has($route_name) ? route($route_name) : url($route_name)) . "' method='{$method}' enctype='{$enctype}' role='form' autocomplete='off'>";
        foreach ($data as $key => $value){
            $value = $this->format($value);

            $html .= "<div class='form-group col-md-2" . ($value['type'] === 'hidden' ? ' hidden' : '') . "'>";
            $html .= "<label>{$value['title']}</label>";
            $html .= $this->buildInput($value);
            $html .= "</div>";
        }
        $html .= "<div class='form-group col-md-2'>";
        $html .= "<label class='w-full'>&nbsp;</label>";
        $html .= "<div class='text-nowrap'>";
        if('get' !== strtolower($method)){
            $html .= csrf_field();
        }
        $html .= "<button type='submit' class='btn btn-sm btn-success'>搜索</button>";
        $html .= "<button type='reset' class='btn btn-sm btn-default m-l-sm'>重置</button>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</form>";

        $html = str_replace(' disabled', '', $html);

        return $html;
    }

    /**
     * 构造table表格
     * @param object $items
     * @param array $title
     * @param array $extra
     * @return string
     */
    protected function buildTable(object $items, array $title = [], array $extra = [])
    {
        $extra = array_merge([
            'table_title' => '数据列表', // table表标题
            'runtime' => true, // 响应时间
            'export' => '', // 导出地址
            'action' => [ // table操作
//                'update' => '', // 更新地址
//                'swal-update' => '', // 更新地址（弹窗模式）
//                'swal-delete' => '', // 删除地址（弹窗模式）
//                'swal-soft-delete' => '', // 伪删除地址（弹窗模式）
//                'swal-restore' => '', // 恢复地址（弹窗模式）
            ],
            'pager' => 'pager', // 分页模版
            'total' => [], // 数据总揽
            'tr_title' => '', // table表行标题，data字段
        ], $extra);
        $html = "";
        $html .= "<div class='panel-heading font-bold'>{$extra['table_title']}";
        if($extra['runtime']){
            $html .= "<font class='text-danger'>（响应时间：" . (microtime(true) - request()->server('REQUEST_TIME_FLOAT')) . "秒）</font>";
        }
        if($extra['export']){
            $html .= "<a href='" . (app('router')->has($extra['export']) ? route($extra['export']) : url($extra['export'])) . "' class='text-success export pull-right'><i class='fa fa-cloud-download'></i>导出数据</a>";
        }
        $html .= "</div>";

        $html .= "<div class='table-responsive'>";
        $html .= "<table class='table table-striped b-light'>";
        if(!empty($title)){
            $html .= "<thead>";
            $html .= "<tr>";
            foreach ($title as $key=>$value){
                if('object' === gettype($value)){
                    $html .= $value(null);
                }else{
                    $html .= "<th>{$value}</th>";
                }
            }
            if($extra['action']){
                $html .= "<th>操作</th>";
            }
            $html .= "</tr>";
            $html .= "</thead>";
            if(!empty($items)){
                $html .= "<tbody>";
                foreach ($items as $key=>$value){
                    if(isset($extra['action']['update'])){
                        $data_update = app('router')->has($extra['action']['update']) ? route($extra['action']['update'], ['id' => $value->id ?? '']) : url($extra['action']['update'], ['id' => $value->id ?? '']);
                    }else if(isset($extra['action']['swal-update'])){
                        $data_update = app('router')->has($extra['action']['swal-update']) ? route($extra['action']['swal-update'], ['id' => $value->id ?? '']) : url($extra['action']['swal-update'], ['id' => $value->id ?? '']);
                    }else{
                        $data_update = '';
                    }
                    $html .= "<tr data-data='" . json_encode($value, JSON_UNESCAPED_UNICODE) . "' data-title='" . ($value[$extra['tr_title']] ?? $extra['tr_title']) . "' " . (empty($data_update) ? "" : "data-update='{$data_update}'") . " " . (($value->deleted_at ?? '') ? "class='text-danger text-l-t'" : (($value->class ?? '') ? "class='{$value->class}'" : "" )) . ">";
                    unset($data_update);
                    foreach ($title as $k=>$val){
                        if(in_array($k, ['logo', 'avatar']) && ($value->$k ?? '')){
                            $html .= "<td><img src='" . ($value->$k ?? '') . "'></td>";
                        }else if(in_array($k, ['status'])){
                            $list = "list_{$k}";
                            if(isset($value->$list) && isset($value->$list[1]) && 2 === count($value->$list)){
                                $html .= "<td>";
                                if($value->deleted_at ?? ''){
                                    $html .= "<label class='i-switch disabled'>";
                                    $html .= "<input type='checkbox' name='{$k}' " . (1 === ($value->$k ?? '') ? 'checked' : '') . " disabled>";
                                    $html .= "<i></i>";
                                    $html .= "</label>";
                                }else{
                                    $html .= "<label class='i-switch '>";
                                    $html .= "<input type='checkbox' name='{$k}' " . (1 === ($value->$k ?? '') ? 'checked' : '') . ">";
                                    $html .= "<i></i>";
                                    $html .= "</label>";
                                }
                                $html .= "</td>";
                            }else{
                                $html .= "<td>" . ($value->$k ?? '') . "</td>";
                            }
                            unset($list);
                        }else if('object' === gettype($val)){
                            $html .= $val($value);
                        }else{
                            $html .= "<td>" . ($value->$k ?? '') . "</td>";
                        }
                    }
                    if($extra['action']){
                        $html .= "<td>";
                        if($value->deleted_at ?? ''){
                            if($extra['action']['swal-restore'] ?? ''){
                                $html .= "<a href='" . (app('router')->has($extra['action']['swal-restore']) ? route($extra['action']['swal-restore'], ['id' => $value->id ?? '']) : url($extra['action']['swal-restore'], ['id' => $value->id ?? ''])) . "' class='btn-link text-primary text-u-l swal-restore'>恢复</a>";
                            }else{
                                $html .= '已删除';
                            }
                        }else{
                            foreach ($extra['action'] as $k=>$val){
                                if('object' === gettype($val)){
                                    $html .= $val($value);
                                    continue;
                                }
                                switch ($k){
                                    case 'swal-restore':
                                        break;
                                    case 'update':
                                        $html .= "<a href='" . (app('router')->has($val) ? route($val, ['id' => $value->id ?? '']) : url($val, ['id' => $value->id ?? ''])) . "' class='btn-link text-success text-u-l'>修改</a>";
                                        break;
                                    case 'swal-update':
                                        $html .= "<a href='" . (app('router')->has($val) ? route($val, ['id' => $value->id ?? '']) : url($val, ['id' => $value->id ?? ''])) . "' class='btn-link text-success text-u-l swal-update'>修改</a>";
                                        break;
                                    case 'swal-delete':
                                        $html .= "<a href='" . (app('router')->has($val) ? route($val, ['id' => $value->id ?? '']) : url($val, ['id' => $value->id ?? ''])) . "' class='btn-link text-danger text-u-l swal-delete'>删除</a>";
                                        break;
                                    case 'swal-soft-delete':
                                        $html .= "<a href='" . (app('router')->has($val) ? route($val, ['id' => $value->id ?? '']) : url($val, ['id' => $value->id ?? ''])) . "' class='btn-link text-danger text-u-l swal-soft-delete'>删除</a>";
                                        break;
                                    default:
                                        if(is_array($val) && isset($val['title']) && isset($val['url'])){
                                            isset($val['type']) || $val['type'] = '';
                                            isset($val['class']) || $val['class'] = 'text-warning';
                                            isset($val['params']) || $val['params'] = [];
                                            if(!empty($val['params'])){
                                                $val['params'] = array_map(function ($val) use ($value){
                                                    return $value->$val ?? '';
                                                }, $val['params']);
                                            }
                                            $html .= "<a href='" . (app('router')->has($val['url']) ? route($val['url'], $val['params']) : url($val['url'], $val['params'])) . "' class='btn-link {$val['class']} text-u-l {$val['type']}'>{$val['title']}</a>";
                                        }else{
                                            $html .= "<a href='" . (app('router')->has($val) ? route($val, ['_id' => $value->id ?? '']) : url($val, ['_id' => $value->id ?? ''])) . "' class='btn-link text-warning text-u-l'>{$k}</a>";
                                        }
                                }
                            }
                        }
                        $html .= "</td>";
                    }
                    $html .= "</tr>";
                }
                $html .= "</tbody>";
            }
            if($extra['total'] && is_array($extra['total'])){
                $html .= "<tfoot>";
                $html .= "<tr class='text-danger'>";
                foreach ($title as $key=>$value){
                    $html .= "<th>" . ($extra['total'][$key] ?? '--') . "</th>";
                }
                if($extra['action']){
                    $html .= "<th>--</th>";
                }
                $html .= "</tr>";
                $html .= "</tfoot>";
            }
        }else{
            if(!empty($items)){
                $html .= "<tbody>";
                foreach ($items as $key=>$value){
                    if(isset($extra['action']['update'])){
                        $data_update = app('router')->has($extra['action']['update']) ? route($extra['action']['update'], ['id' => $value->id ?? '']) : url($extra['action']['update'], ['id' => $value->id ?? '']);
                    }else if(isset($extra['action']['swal-update'])){
                        $data_update = app('router')->has($extra['action']['swal-update']) ? route($extra['action']['swal-update'], ['id' => $value->id ?? '']) : url($extra['action']['swal-update'], ['id' => $value->id ?? '']);
                    }else{
                        $data_update = '';
                    }
                    $html .= "<tr data-data='" . json_encode($value, JSON_UNESCAPED_UNICODE) . "' data-title='" . ($value[$extra['tr_title']] ?? $extra['tr_title']) . "' " . (empty($data_update) ? "" : "data-update='{$data_update}'") . " " . (($value->deleted_at ?? '') ? "class='text-danger text-l-t'" : '') . ">";
                    unset($data_update);
                    foreach ($value as $k=>$val){
                        if($k === 'file_id' && !empty($val)){
                            $html .= "<td><img src='{$val}'></td>";
                        }else if(in_array($k, ['status'])){
                            $list = "list_{$k}";
                            if(isset($value->$list) && isset($value->$list[1]) && 2 === count($value->$list)){
                                $html .= "<td>";
                                if($value->deleted_at ?? ''){
                                    $html .= "<label class='i-switch disabled'>";
                                    $html .= "<input type='checkbox' name='{$k}' " . (1 === $val ? 'checked' : '') . " disabled>";
                                    $html .= "<i></i>";
                                    $html .= "</label>";
                                }else{
                                    $html .= "<label class='i-switch '>";
                                    $html .= "<input type='checkbox' name='{$k}' " . (1 === $val ? 'checked' : '') . ">";
                                    $html .= "<i></i>";
                                    $html .= "</label>";
                                }
                                $html .= "</td>";
                            }else{
                                $html .= "<td>{$val}</td>";
                            }
                            unset($list);
                        }else{
                            $html .= "<td>{$val}</td>";
                        }
                    }
                    if($extra['action']){
                        $html .= "<td>";
                        if($value->deleted_at ?? ''){
                            if($extra['action']['swal-restore'] ?? ''){
                                $html .= "<a href='" . (app('router')->has($extra['action']['swal-restore']) ? route($extra['action']['swal-restore'], ['id' => $value->id ?? '']) : url($extra['action']['swal-restore'], ['id' => $value->id ?? ''])) . "' class='btn-link text-primary text-u-l swal-restore'>恢复</a>";
                            }else{
                                $html .= '已删除';
                            }
                        }else{
                            foreach ($extra['action'] as $k=>$val){
                                if('object' === gettype($val)){
                                    $html .= $val($value);
                                    continue;
                                }
                                switch ($k){
                                    case 'swal-restore':
                                        break;
                                    case 'update':
                                        $html .= "<a href='" . (app('router')->has($val) ? route($val, ['id' => $value->id ?? '']) : url($val, ['id' => $value->id ?? ''])) . "' class='btn-link text-success text-u-l'>修改</a>";
                                        break;
                                    case 'swal-update':
                                        $html .= "<a href='" . (app('router')->has($val) ? route($val, ['id' => $value->id ?? '']) : url($val, ['id' => $value->id ?? ''])) . "' class='btn-link text-success text-u-l swal-update'>修改</a>";
                                        break;
                                    case 'swal-delete':
                                        $html .= "<a href='" . (app('router')->has($val) ? route($val, ['id' => $value->id ?? '']) : url($val, ['id' => $value->id ?? ''])) . "' class='btn-link text-danger text-u-l swal-delete'>删除</a>";
                                        break;
                                    case 'swal-soft-delete':
                                        $html .= "<a href='" . (app('router')->has($val) ? route($val, ['id' => $value->id ?? '']) : url($val, ['id' => $value->id ?? ''])) . "' class='btn-link text-danger text-u-l swal-soft-delete'>删除</a>";
                                        break;
                                    default:
                                        if(is_array($val) && isset($val['title']) && isset($val['url'])){
                                            isset($val['type']) || $val['type'] = '';
                                            isset($val['class']) || $val['class'] = 'text-warning';
                                            isset($val['params']) || $val['params'] = [];
                                            if(!empty($val['params'])){
                                                $val['params'] = array_map(function ($val) use ($value){
                                                    return $value->$val ?? '';
                                                }, $val['params']);
                                            }
                                            $html .= "<a href='" . (app('router')->has($val['url']) ? route($val['url'], $val['params']) : url($val['url'], $val['params'])) . "' class='btn-link {$val['class']} text-u-l {$val['type']}'>{$val['title']}</a>";
                                        }else{
                                            $html .= "<a href='" . (app('router')->has($val) ? route($val, ['_id' => $value->id ?? '']) : url($val, ['_id' => $value->id ?? ''])) . "' class='btn-link text-warning text-u-l'>{$k}</a>";
                                        }
                                }
                            }
                        }
                        $html .= "</td>";
                    }
                    $html .= "</tr>";
                }
                $html .= "</tbody>";
            }
            if($extra['total'] && is_array($extra['total'])){
                $html .= "<tfoot>";
                $html .= "<tr class='text-danger'>";
                foreach ($extra['total'] as $key=>$value){
                    $html .= "<th>{$value}</th>";
                }
                if($extra['action']){
                    $html .= "<th>--</th>";
                }
                $html .= "</tr>";
                $html .= "</tfoot>";
            }
        }
        $html .= "</table>";
        $html .= "</div>";
        if($extra['pager']){
            $html .= "<footer class='panel-footer'>";
            $html .= $items->appends(request()->input())->onEachSide(1)->links($extra['pager']);
            $html .= "</footer>";
        }

        return $html;
    }

    /**
     * 构造form表单
     * @param array $data
     * @param null $route_name
     * @param string $method
     * @param string $enctype
     * @return string
     */
    protected function buildForm(array $data, $route_name=null, $method='post', $enctype='application/x-www-form-urlencoded')
    {
        if(is_null($route_name)) $route_name = request()->route()->getName();
        $func_id = function () use ($data, $route_name){
            $id = null;
            $data = array_reverse($data); // 翻转顺序，一般最后定义ID
            foreach ($data as $key=>$value){
                if(!is_array($value)) continue;
                if(isset($value['name']) && 'id' === $value['name']){
                    $id = $value['value'] ?? '';
                    break;
                }
            }
            return $id ?: null;
        };
        $html = "";
        $html .= "<form action='" . (app('router')->has($route_name) ? route($route_name, ['id' => $func_id()]) : url($route_name, ['id' => $func_id()])) . "' method='{$method}' enctype='{$enctype}' class='form-horizontal' role='form' autocomplete='off'>";
        $html .= "<div class='line line-dashed b-b line-lg pull-in'></div>";
        foreach ($data as $key => $value){
            if('object' === gettype($value)){
                $value = $value();
            }
            if(is_array($value)){
                $html .= $this->buildGroupInput($value, 2);
            }else{
                $html .= $value;
            }
        }
        $html .= "<div class='line line-dashed b-b line-lg pull-in'></div>";
        $html .= "<div class='form-group'>";
        $html .= "<div class='col-sm-4 col-sm-offset-2'>";
        if('get' !== strtolower($method)){
            $html .= csrf_field();
        }
        $html .= "<button type='reset' class='btn btn-default'>重置</button>";
        $html .= "<button type='submit' class='btn btn-success'>提交</button>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</form>";

        return $html;
    }

    /**
     * 构造swal弹窗form表单
     * @param array $data
     * @param null $route_name
     * @param string $method
     * @return string
     */
    protected function buildSwalForm(array $data, $route_name=null, $method='post', $enctype='application/x-www-form-urlencoded')
    {
        if(is_null($route_name)) $route_name = request()->route()->getName();
        $func_id = function () use ($data, $route_name){
            $id = null;
            $data = array_reverse($data); // 翻转顺序，一般最后定义ID
            foreach ($data as $key=>$value){
                if(!is_array($value)) continue;
                if(isset($value['name']) && 'id' === $value['name']){
                    $id = $value['value'] ?? '';
                    break;
                }
            }
            return $id ?: null;
        };
        $html = "";
        $html .= "<form action='" . (app('router')->has($route_name) ? route($route_name, ['id' => $func_id()]) : url($route_name, ['id' => $func_id()])) . "' method='{$method}' enctype='{$enctype}' class='form-horizontal' role='form' autocomplete='off'>";
        $html .= "<div class='line line-dashed b-b line-lg'></div>";
        foreach ($data as $key => $value){
            if('object' === gettype($value)){
                $value = $value();
            }
            if(is_array($value)){
                $html .= $this->buildGroupInput($value, 3);
            }else{
                $html .= $value;
            }
        }
        $html .= "<div class='line line-dashed b-b line-lg'></div>";
        $html .= "</form>";

        return $html;
    }

    /**
     * 格式化input表单构造参数
     * @param array $data
     * @return array
     */
    protected function format(array $data)
    {
        array_key_exists('name', $data) || $data['name'] = '';
        array_key_exists('title', $data) || $data['title'] = $data['name'];
        array_key_exists('value', $data) || $data['value'] = $data['name'] ? request()->input($data['name']) : '';
        array_key_exists('type', $data) || $data['type'] = 'text';
        array_key_exists('list', $data) || $data['list'] = [];
        array_key_exists('search', $data) || $data['search'] = false;
        array_key_exists('disabled', $data) || $data['disabled'] = '';
        array_key_exists('multiple', $data) || $data['multiple'] = '';
        array_key_exists('optgroup', $data) || $data['optgroup'] = false;
        if(!empty($data['disabled'])){
            $data['disabled'] = 'disabled';
        }else{
            $data['disabled'] = '';
        }
        if(!empty($data['multiple'])){
            $data['multiple'] = 'multiple';
        }else{
            $data['multiple'] = '';
        }
        return $data;
    }

    /**
     * 构造input表单
     * @param array $data
     */
    protected function buildGroupInput(array $data, int $grid = 2)
    {
        $data = $this->format($data);

        $html = "";
        if('line' === $data['type']){
            $html .= "<div class='line line-dashed b-b line-lg'></div>";
        }else{
            $html .= "<div class='form-group" . ($data['type'] === 'hidden' ? ' hidden' : '') . "'>";
            $html .= "<label class='col-sm-{$grid} control-label'>{$data['title']}</label>";
            $html .= "<div class='col-sm-" . (12 - $grid) . " text-left" . (in_array($data['type'], ['file_image', 'file_images']) ? ' upload-image' : '') . "'>";
            $html .= $this->buildInput($data);
            $html .= "</div>";
            $html .= "</div>";
        }
        return $html;
    }

    /**
     * 构造input表单
     * @param array $data
     */
    protected function buildInput(array $data)
    {
        $data = $this->format($data);

        $html = "";
        switch ($data['type']) {
            case "checkbox":
            case "radio":
                if(isset($data['list']) && !empty($data['list'])){
                    is_array($data['value']) || $data['value'] = [$data['value']];
                    if('array' === gettype($data['list'])){
                        foreach ($data['list'] as $k=>$val){
                            $html .= "<label class='checkbox-inline i-checks {$data['disabled']}'><input type='{$data['type']}' name='{$data['name']}' value='{$k}' data-title='{$val}' " . (in_array($k, $data['value']) ? 'checked' : '') . " {$data['disabled']}><i></i>{$val}</label>";
                        }
                    }else if('object' === gettype($data['list'])){
                        foreach ($data['list'] as $k=>$val){
                            $html .= "<label class='checkbox-inline i-checks " . (2 === ($val->status ?? '') ? 'disabled' : $data['disabled']) . "'><input type='{$data['type']}' name='{$data['name']}' value='" . ($val->id ?? '') . "' data-title='" . ($val->title ?? '') . "' " . (in_array($val->id ?? '', $data['value']) ? 'checked' : '') . " " . (2 === ($val->status ?? '') ? 'disabled' : $data['disabled']) . "><i></i>"  . ($val->title ?? '') . "</label>";
                        }
                    }
                }
                break;
            case "switch":
            case "checkbox-switch":
                if(isset($data['list']) && !empty($data['list'])){
                    is_array($data['value']) || $data['value'] = [$data['value']];
                    if('array' === gettype($data['list'])){
                        foreach ($data['list'] as $k=>$val){
                            $html .= "<label class='i-switch {$data['disabled']} m-t-xs m-r'><input type='checkbox' name='{$data['name']}' value='{$k}' " . (in_array($k, $data['value']) ? 'checked' : '') . " {$data['disabled']}><i></i></label>";
                        }
                    }else if('object' === gettype($data['list'])){
                        foreach ($data['list'] as $k=>$val){
                            $html .= "<label class='i-switch " . (2 === ($val->status ?? '') ? 'disabled' : $data['disabled']) . " m-t-xs m-r'><input type='checkbox' name='{$data['name']}' value='" . ($val->id ?? '') . "' " . (in_array($val->id ?? '', $data['value']) ? 'checked' : '') . " " . (2 === ($val->status ?? '') ? 'disabled' : $data['disabled']) . "><i></i></label>";
                        }
                    }
                }
                break;
            case "radio-switch":
                if(isset($data['list']) && !empty($data['list'])){
                    if('array' === gettype($data['list'])){
                        foreach ($data['list'] as $k=>$val){
                            $html .= "<label class='i-switch {$data['disabled']} m-t-xs m-r'><input type='radio' name='{$data['name']}' value='{$k}' " . ($k == $data['value'] ? 'checked' : '') . " {$data['disabled']}><i></i></label>";
                        }
                    }else if('object' === gettype($data['list'])){
                        foreach ($data['list'] as $k=>$val){
                            $html .= "<label class='i-switch " . (2 === ($val->status ?? '') ? 'disabled' : $data['disabled']) . " m-t-xs m-r'><input type='radio' name='{$data['name']}'  value='" . ($val->id ?? '') . "' " . (($val->id ?? '') == $data['value'] ? 'checked' : '') . " " . (2 === ($val->status ?? '') ? 'disabled' : $data['disabled']) . "><i></i></label>";
                        }
                    }
                }
                break;
            case "select":
                is_array($data['value']) || $data['value'] = [$data['value']];
                $html .= "<select name='{$data['name']}" . ($data['multiple'] ? '[]' : '') . "' class='form-control m-b' {$data['disabled']} {$data['multiple']} data-live-search='{$data['search']}'>";
                if('multiple' !== $data['multiple']){
                    $html .= "<option value=''>- 请选择{$data['title']} -</option>";
                }
                if(isset($data['list']) && !empty($data['list'])){
                    if('array' === gettype($data['list'])){
                        foreach ($data['list'] as $k=>$val){
                            $html .= "<option value='{$k}' " . (in_array($k, $data['value']) ? 'selected' : '') . " data-title='{$val}'>{$k} - {$val}</option>";
                        }
                    }else if('object' === gettype($data['list'])){
                        if($data['optgroup']){
                            $list_optgroup = [];
                            $optgroup = $data['optgroup'];
                            foreach ($data['list'] as $k=>$val){
                                $list_optgroup[$val->$optgroup ?? $optgroup][] = "<option value='" . ($val->id ?? '') . "' " . (in_array($val->id ?? '', $data['value']) ? 'selected' : '') . " " . (2 === ($val->status ?? '') ? 'disabled' : '') . " data-title='" . ($val->title ?? '') . "'>" . ($val->id ?? '') . ' - ' . ($val->title ?? '') . "</option>";
                            }
                            foreach ($list_optgroup as $k=>$val){
                                $html .= "<optgroup label='{$k}'>";
                                $html .= implode('', $val);
                                $html .= "</optgroup>";
                            }
                            unset($list_optgroup, $optgroup);
                        }else{
                            foreach ($data['list'] as $k=>$val){
                                $html .= "<option value='" . ($val->id ?? '') . "' " . (in_array($val->id ?? '', $data['value']) ? 'selected' : '') . " " . (2 === ($val->status ?? '') ? 'disabled' : '') . " data-title='" . ($val->title ?? '') . "'>" . ($val->id ?? '') . ' - ' . ($val->title ?? '') . "</option>";
                            }
                        }
                    }
                }
                $html .= "</select>";
                break;
            case "textarea":
                $html .= "<textarea name='{$data['name']}' {$data['disabled']} class='form-control' rows='3' placeholder='请输入{$data['title']}...'>{$data['value']}</textarea>";
                break;
            case "file_image":
                if('multiple' === $data['multiple']){
                    if(isset($data['list']) && !empty($data['list'])){
                        if('array' === gettype($data['list'])){
                            foreach ($data['list'] as $k=>$val){
                                $html .= "<div class='btn-group' data-id='{$k}'>";
                                $html .= "<div class='thumb-lg'>";
                                $html .= "<img src='" . ($val ?: asset(static_path('admin') . '/img/upload.png')) . "'>";
                                $html .= "</div>";
                                $html .= "<div class='text-center'>";
                                $html .= "<a class='btn btn-danger btn-xs pull-left'><i class='fa fa-trash-o'></i>删除</a>";
                                $html .= "<a class='btn btn-info btn-xs pull-right'><i class='fa fa-arrow-circle-o-up'></i>上传</a>";
                                $html .= "</div>";
                                $html .= "</div>";
                            }
                        }else if('object' === gettype($data['list'])){
                            foreach ($data['list'] as $k=>$val){
                                $html .= "<div class='btn-group' data-id='" . ($val->id ?? '') . "'>";
                                $html .= "<div class='thumb-lg'>";
                                $html .= "<img src='" . (route('files.image', ['sn' => encrypt($val->id ?? '')])) . "'>";
                                $html .= "</div>";
                                $html .= "<div class='text-center'>";
                                $html .= "<a class='btn btn-danger btn-xs pull-left'><i class='fa fa-trash-o'></i>删除</a>";
                                $html .= "<a class='btn btn-info btn-xs pull-right'><i class='fa fa-arrow-circle-o-up'></i>上传</a>";
                                $html .= "</div>";
                                $html .= "</div>";
                            }
                        }
                    }
                    $html .= "<div class='btn-group'>";
                    $html .= "<div class='thumb-lg'>";
                    $html .= "<img src='" . asset(static_path('admin') . '/img/upload.png') . "'>";
                    $html .= "</div>";
                    $html .= "<div class='text-center'>";
                    $html .= "<a class='btn btn-danger btn-xs pull-left'><i class='fa fa-trash-o'></i>删除</a>";
                    $html .= "<a class='btn btn-info btn-xs pull-right'><i class='fa fa-arrow-circle-o-up'></i>上传</a>";
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "<input type='file' name='{$data['name']}" . ($data['multiple'] ? '[]' : '') . "' {$data['multiple']} accept='image/*' {$data['disabled']} class='hidden'>";
                }else{
                    $html .= "<div class='btn-group'>";
                    $html .= "<div class='thumb-lg'>";
                    $html .= "<img src='" . ($data['value'] ?: asset(static_path('admin') . '/img/upload.png')) . "'>";
                    $html .= "</div>";
                    $html .= "<div class='text-center'>";
                    $html .= "<a class='btn btn-danger btn-xs pull-left'><i class='fa fa-trash-o'></i>删除</a>";
                    $html .= "<a class='btn btn-info btn-xs pull-right'><i class='fa fa-arrow-circle-o-up'></i>上传</a>";
                    $html .= "</div>";
                    $html .= "</div>";
                    $html .= "<input type='file' name='{$data['name']}' accept='image/*' class='hidden'>";
                }
                break;
            case "file":
            case "text":
            case "password":
            case "hidden":
            default:
                $html .= "<input type='{$data['type']}' name='{$data['name']}' value='{$data['value']}' {$data['disabled']} class='form-control' placeholder='请输入{$data['title']}...'>";
        }
        return $html;
    }
}
