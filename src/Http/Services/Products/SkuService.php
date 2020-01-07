<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:46 上午
 */


namespace Z1px\App\Http\Services\Products;


use Z1px\App\Models\Products\SkuModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class SkuService extends SkuModel
{

    use ToInfo, ToAdd, ToUpdate, ToList, ToRestore;

    protected function toInfoParams(array $params, $id=null)
    {
        if(!is_null($id)){
            $params['id'] = $id;
        }

        return $params;
    }

    protected function toUpdating()
    {
        $list_specs2 = request()->input('specs');
        if(count(array_filter($list_specs2)) !== count($list_specs2)){
            return [
                'code' => 0,
                'message' => '销售属性不能为空',
            ];
        }

        app('db')->beginTransaction();
        try{
            $sync = array_map(function ($value){
                return ['title' => $value];
            }, $list_specs2);
            unset($list_specs2);
            // 保存图片
            if(request()->input('logo') && is_array(request()->input('logo'))){
                foreach (request()->input('logo') as $key=>$value){
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if(false === $value && $key){
                        if(isset($sync[$key])){
                            $sync[$key]['file_id'] = 0;
                            app('files_service')->toInvisible(app('sku_specs_service')->where('sku_id', $this->id)->where('spec_id', $key)->value('file_id'));
                        }
                    }
                    unset($key, $value);
                }
            }
            $files = app('files_service')->toAdd($this, 'logo', 'sku_logo');
            if(!empty($files)){
                foreach ($files as $key=>$value){
                    if(isset($sync[$value['_key']])){
                        $sync[$value['_key']]['file_id'] = $value['id'];
                        app('files_service')->toInvisible(app('sku_specs_service')->where('sku_id', $this->id)->where('spec_id', $value['_key'])->value('file_id'));
                    }
                }
            }
            unset($files);
            $this->specs()->syncWithoutDetaching($sync); // 更新中间表
            unset($sync);

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

    // 进货
    public function toIn()
    {
        $params = request()->input();

        // 参数合法性验证
        validator($params, $this->rules('in'), $this->messages('in'), $this->attributes())->validate();

        $data = $this->find($params['id']);
        if(empty($data)){
            return [
                'code' => 0,
                'message' => '进货失败，数据不存在！'
            ];
        }
        $data->setBeforeAttributes($data->getAttributes());

        app('db')->beginTransaction();
        try{
            $data->stock()->create([
                'spu_id' => $data->spu_id,
                'sn' => $data->sn,
                'price' => $params['price'],
                'stock' => $params['stock'],
                'type' => 1,
                'remark' => $params['remark'] ?? '',
            ]);
            $data->increment('stock', $params['stock']);
            app('tables_operated_service')->toAdd($data, 'update', ['change_attr' => array_diff_assoc($data->getAttributes(), $data->getBeforeAttributes())]);
            app('db')->commit();
            $result = [
                'code' => 1,
                'message' => '更新成功',
                'data' => $data->getAttributes()
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

    // 退货
    public function toOut()
    {
        $params = request()->input();

        // 参数合法性验证
        validator($params, $this->rules('out'), $this->messages('out'), $this->attributes())->validate();

        $data = $this->find($params['id']);
        if(empty($data)){
            return [
                'code' => 0,
                'message' => '退货失败，数据不存在！'
            ];
        }
        $data->setBeforeAttributes($data->getAttributes());

        if($data->stock < $params['stock']){
            return [
                'code' => 0,
                'message' => "退货失败，库存数量只有{$data->stock}！"
            ];
        }

        app('db')->beginTransaction();
        try{
            $data->stock()->create([
                'spu_id' => $data->spu_id,
                'sn' => $data->sn,
                'price' => $params['price'],
                'stock' => $params['stock'],
                'type' => 2,
                'remark' => $params['remark'] ?? '',
            ]);
            $data->decrement('stock', $params['stock']);
            app('tables_operated_service')->toAdd($data, 'update', ['change_attr' => array_diff_assoc($data->getAttributes(), $data->getBeforeAttributes())]);
            app('db')->commit();
            $result = [
                'code' => 1,
                'message' => '更新成功',
                'data' => $data->getAttributes()
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
