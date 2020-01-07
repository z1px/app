<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:45 上午
 */


namespace Z1px\App\Http\Services\Products;


use Z1px\App\Models\Products\SpuModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class SpuService extends SpuModel
{

    use ToInfo, ToAdd, ToUpdate, ToList, ToRestore;

    /**
     * 新增数据前
     * @return array
     */
    protected function toAdding()
    {
        // 销售属性检测
        $list_specs2 = request()->input('specs2');
        if(empty($list_specs2)){
            return [
                'code' => 0,
                'message' => '销售属性必须',
            ];
        }
        if(!is_array($list_specs2)){
            return [
                'code' => 0,
                'message' => '销售属性异常',
            ];
        }
        if(count(array_filter($list_specs2)) !== count($list_specs2)){
            return [
                'code' => 0,
                'message' => '销售属性必选',
            ];
        }

        // 保存图片
        $file = app('files_service')->toAdd($this, 'image', 'spu');

        app('db')->beginTransaction();
        try{
            $list_file = [];
            if(!empty($file)){
                $this->setAttribute('file_id', $file['id']);
                $list_file[] = $file['id'];
            }
            unset($file);

            // 处理详情页图片// 处理详情页图片
            $description = urldecode($this->getAttribute('description'));
            preg_match_all("/<img.*?src=[\'|\"](data:([a-z]+\/[a-z0-9-+.]+(;[a-z-]+=[a-z0-9-]+)?)?(;base64)?,([a-z0-9!$&',()*+;=\-._~:@\/?%\s]*?))[\'|\"].*?[\/]?>/i", $description, $match);
            if(isset($match[1]) && !empty($match[1])){
                foreach ($match[1] as $value){
                    $file = app('files_service')->toAddBase64($this, $value, 'spu_desc');
                    if(!empty($file)){
                        $description = str_replace($value, $this->file_to_image($file['id']), $description);
                        $list_file[] = $file['id'];
                    }
                    unset($file);
                }
            }
            $this->setAttribute('description', $description);
            unset($description);

            $this->save();
            if(!empty($list_file)){
                foreach ($list_file as $value){
                    app('files_service')->toUpdate($value, $this->id);
                }
            }
            unset($list_file);

            $files = app('files_service')->toAdd($this, 'images', 'spu_files');
            if(!empty($files)){
                $this->images()->createMany(array_map(function ($data){
                    return ['file_id' => $data['id']];
                }, $files));
            }
            unset($files);

            // 销售属性处理
            $list_specs = [];
            foreach ($list_specs2 as $key=>$value){
                if(!is_array($value)){
                    $value = explode(',', $value);
                }
                foreach ($value as $spec_id){
                    $list_specs[$spec_id] = app('specs_service')->select(['attribute_id', 'title'])->find($spec_id)->toArray();
                    unset($spec_id);
                }
                unset($key, $value);
            }
            if(!empty($list_specs)){
                $this->specs()->attach($list_specs);
            }
            unset($list_specs2, $list_specs);

            // 属性处理
            $list_specs1 = request()->input('specs1');
            $list_specs3 = request()->input('specs3');
            $list_attributes = [];
            if(!empty($list_specs1) && is_array($list_specs1)){
                foreach ($list_specs1 as $key=>$value){
                    $list_attributes[$key] = [
                        'spec_id' => $value ?: 0,
                        'title' => $value ? app('specs_service')->where('id', $value)->value('title') : '',
                        'type' => 1,
                    ];
                    unset($key, $value);
                }
            }
            if(!empty($list_specs3) && is_array($list_specs3)){
                foreach ($list_specs3 as $key=>$value){
                    $list_attributes[$key] = [
                        'spec_id' => 0,
                        'title' => $value,
                        'type' => 3,
                    ];
                    unset($key, $value);
                }
            }
            if(!empty($list_attributes)){
                $this->attrs()->attach($list_attributes);
            }
            unset($list_specs1, $list_specs3, $list_attributes);

            // 保存服务
            $list_services = request()->input('service_id');
            if(is_string($list_services)){
                $list_services = explode(',', $list_services);
            }
            if(is_array($list_services) && !empty($list_services)){
                $this->services()->attach($list_services);
            }
            unset($list_services);

            // 库存管理
            $sku_specs = request()->input('sku_specs');
            if(!empty($sku_specs) && is_array($sku_specs)){
                foreach ($sku_specs as $key=>$value){
                    if(empty($value) || !is_numeric($value) || $value <= 0) continue;
                    $key = urldecode($key);
                    $specs = app('specs_service')->whereIn('id', explode(',', $key))->orderByRaw("field(id," . $key . ") asc")->get();
                    $title = $this->title;
                    $attach = [];
                    foreach ($specs as $k=>$val){
                        $title .= " {$val->title}";
                        $attach[$val->id] = [
                            'spu_id' => $this->id,
                            'attribute_id' => $val->attribute_id,
                            'title' => $val->title,
                            'file_id' => $val->file_id,
                        ];
                    }
                    $sku = $this->sku()->create([
                        'sn' => '',
                        'title' => $title,
                        'price' => $value,
                        'stock' => 0,
                    ]);
                    $sku->specs()->attach($attach);
                    unset($specs, $title, $attach, $sku);
                }
            }

            app('db')->commit();
            $result = [
                'code' => 1,
                'message' => '添加成功',
                'data' => $this->getAttributes()
            ];
        }catch (\Exception $exception){
            app('db')->rollBack();
            $result = [
                'code' => 0,
                'message' => '添加失败'
            ];
        }
        return $result;
    }

    /**
     * 更新数据前
     * @return array
     */
    protected function toUpdating()
    {
        // 销售属性检测
        $list_specs2 = request()->input('specs2');
        if(empty($list_specs2)){
            return [
                'code' => 0,
                'message' => '销售属性必须',
            ];
        }
        if(!is_array($list_specs2)){
            return [
                'code' => 0,
                'message' => '销售属性异常',
            ];
        }
        if(count(array_filter($list_specs2)) !== count($list_specs2)){
            return [
                'code' => 0,
                'message' => '销售属性必选',
            ];
        }

        app('db')->beginTransaction();
        try{
            if(request()->input('image') && false === filter_var(request()->input('image'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)){
                $this->setAttribute('file_id', 0);
            }else{
                $file = app('files_service')->toAdd($this, 'image', 'spu');
                if(!empty($file)){
                    $this->setAttribute('file_id', $file['id']);
                }
                unset($file);
            }

            $before_file_id = $this->getBeforeAttribute('file_id');
            if(!empty($before_file_id)){
                if($this->file_id !== $before_file_id){
                    app('files_service')->toInvisible($before_file_id);
                }
            }
            unset($before_file_id);

            // 保存图片
            $files = app('files_service')->toAdd($this, 'images', 'spu_files');
            if(!empty($files)){
                $this->images()->createMany(array_map(function ($data){
                    return ['file_id' => $data['id']];
                }, $files));
            }
            unset($files);
            if(request()->input('images') && is_array(request()->input('images'))){
                foreach (request()->input('images') as $key=>$value){
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if(false === $value && $key){
                        app('files_service')->toInvisible($key);
                        $this->images()->where('file_id', $key)->delete();
                    }
                    unset($key, $value);
                }
            }

            // 处理详情页图片// 处理详情页图片
            $description = urldecode($this->getAttribute('description'));
            preg_match_all("/<img.*?src=[\'|\"](data:([a-z]+\/[a-z0-9-+.]+(;[a-z-]+=[a-z0-9-]+)?)?(;base64)?,([a-z0-9!$&',()*+;=\-._~:@\/?%\s]*?))[\'|\"].*?[\/]?>/i", $description, $match);
            if(isset($match[1]) && !empty($match[1])){
                foreach ($match[1] as $value){
                    $file = app('files_service')->toAddBase64($this, $value, 'spu_desc');
                    if(!empty($file)){
                        $description = str_replace($value, $this->file_to_image($file['id']), $description);
                    }
                    unset($file);
                }
            }
            $this->setAttribute('description', $description);
            unset($description);

            // 销售属性处理
            $list_specs = [];
            foreach ($list_specs2 as $key=>$value){
                if(!is_array($value)){
                    $value = explode(',', $value);
                }
                foreach ($value as $spec_id){
                    $list_specs[$spec_id] = app('specs_service')->select(['attribute_id', 'title'])->find($spec_id)->toArray();
                    unset($spec_id);
                }
                unset($key, $value);
            }
            if(!empty($list_specs)){
                $list_pivot_specs = $this->specs()->pluck('spec_id', 'spec_id')->toArray(); // 中间表已存在属性ID
                $attach = array_diff_key($list_specs, $list_pivot_specs);
                if(!empty($attach)){
                    $this->specs()->attach($attach); // 添加中间表中未添加的ID
                }
                unset($list_pivot_specs, $attach);
            }
            unset($list_specs2, $list_specs);

            // 属性处理
            $list_specs1 = request()->input('specs1');
            $list_specs3 = request()->input('specs3');
            $list_attributes = [];
            if(!empty($list_specs1) && is_array($list_specs1)){
                foreach ($list_specs1 as $key=>$value){
                    $list_attributes[$key] = [
                        'spec_id' => $value ?: 0,
                        'title' => $value ? app('specs_service')->where('id', $value)->value('title') : '',
                        'type' => 1,
                    ];
                    unset($key, $value);
                }
            }
            if(!empty($list_specs3) && is_array($list_specs3)){
                foreach ($list_specs3 as $key=>$value){
                    $list_attributes[$key] = [
                        'spec_id' => 0,
                        'title' => $value,
                        'type' => 3,
                    ];
                    unset($key, $value);
                }
            }
            if(empty($list_attributes)){
                $this->attrs()->detach();
            }else{
                $list_pivot_attributes = $this->attrs()->pluck('attribute_id', 'attribute_id')->toArray(); // 中间表已存在属性ID
                if(empty($list_pivot_attributes)){
                    $this->attrs()->attach($list_attributes);
                }else{
                    $detach = array_diff_key($list_pivot_attributes, $list_attributes);
                    if(!empty($detach)){
                        $this->attrs()->detach($detach); // 删除中间表中未选择的ID
                    }
                    $attach = array_diff_key($list_attributes, $list_pivot_attributes);
                    if(!empty($attach)){
                        $this->attrs()->attach($attach); // 添加中间表中未添加的ID
                    }
                    $sync = array_intersect_key($list_attributes, $list_pivot_attributes);
                    if(!empty($sync)){
                        $this->attrs()->syncWithoutDetaching($sync); // 更新中间表
                    }
                    unset($detach, $attach, $sync);
                }
                unset($list_pivot_attributes);
            }
            unset($list_specs1, $list_specs3, $list_attributes);

            // 保存服务
            $list_services = request()->input('service_id');
            if(is_string($list_services)){
                $list_services = explode(',', $list_services);
            }
            if(is_array($list_services) && !empty($list_services)){
                $list_pivot_services = $this->services()->pluck('service_id')->toArray(); // 中间表已存在属性ID
                if(empty($list_pivot_services)){
                    $this->services()->attach($list_services);
                }else{
                    $detach = array_diff($list_pivot_services, $list_services);
                    if(!empty($detach)){
                        $this->services()->detach($detach); // 删除中间表中未选择的ID
                    }
                    $attach = array_diff($list_services, $list_pivot_services);
                    if(!empty($attach)){
                        $this->services()->attach($attach); // 添加中间表中未添加的ID
                    }
                    unset($detach, $attach);
                }
                unset($list_pivot_services);
            }else{
                $this->services()->detach();
            }
            unset($list_services);

            // 库存管理
            $sku_specs = request()->input('sku_specs');
            if(!empty($sku_specs) && is_array($sku_specs)){
                $list_sku = $this->sku()->get();
                $list_sku_specs = [];
                foreach ($list_sku as $key=>$value){
                    $key = $value->specs()->orderBy($value->specs()->getTable() . '.id', 'asc')->pluck('spec_id')->toArray();
                    $list_sku_specs[implode(',', $key)] = $value;
                }
                unset($list_sku);
                foreach ($sku_specs as $key=>$value){
                    if(empty($value) || !is_numeric($value) || $value <= 0) continue;
                    $key = urldecode($key);
                    if(isset($list_sku_specs[$key])){
                        $sku = $list_sku_specs[$key];
                        $sku->setAttribute('price', $value);
                        $sku->save();
                    }else{
                        $specs = app('specs_service')->whereIn('id', explode(',', $key))->orderByRaw("field(id," . $key . ") asc")->get();
                        $title = $this->title;
                        $attach = [];
                        foreach ($specs as $k=>$val){
                            $title .= " {$val->title}";
                            $attach[$val->id] = [
                                'spu_id' => $this->id,
                                'attribute_id' => $val->attribute_id,
                                'title' => $val->title,
                                'file_id' => $val->file_id,
                            ];
                        }
                        $sku = $this->sku()->create([
                            'sn' => '',
                            'title' => $title,
                            'price' => $value,
                            'stock' => 0,
                        ]);
                        $sku->specs()->attach($attach);
                        unset($specs, $title, $attach, $sku);
                    }
                }
            }

            $this->save();
            app('db')->commit();
            $result = [
                'code' => 1,
                'message' => '更新成功',
                'data' => $this->getAttributes()
            ];
        }catch (\Exception $exception){
            app('db')->rollBack();
            $result = [
                'code' => 0,
                'message' => '更新失败'
            ];
        }
        return $result;
    }

}
