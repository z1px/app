<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:45 上午
 */


namespace Z1px\App\Http\Services\Products;


use Z1px\App\Models\Products\CategoryModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToListAll;
use Z1px\App\Traits\Eloquent\ToUpdate;

class CategoryService extends CategoryModel
{

    use ToInfo, ToAdd, ToUpdate, ToList, ToListAll;

    protected function toListParams(array $params)
    {
        if(isset($params['tid']) && !empty($params['tid']) && (!isset($params['pid']) || empty($params['pid']))){
            $params['pid'] = $params['tid'];
            $params['pid'] = $this->where('pid', $params['tid'])->pluck('id')->toArray();
            array_push($params['pid'], intval($params['tid']));
        }
        return $params;
    }


    /**
     * 获取数据前修改数据
     * @param array $params
     * @param null $id
     * @return array
     */
    protected function toInfoParams(array $params, $id=null)
    {
        if(!is_null($id)){
            $params['id'] = $id;
        }

        return $params;
    }

    /**
     * 新增数据前修改参数
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    protected function toAddParams($params)
    {
        if(isset($params['pid']) && !empty($params['pid'])){
            $data = app('category_service')->toInfo($params['pid']);
            if(empty($data)){
                throw new \Exception('上级分类不存在！');
            }else{
                $params['pid'] = $data->id;
                $params['level'] = $data->level + 1;
            }
        }else if(isset($params['tid']) && !empty($params['tid'])){
            $data = app('category_service')->toInfo($params['tid']);
            if(empty($data)){
                throw new \Exception('上级分类不存在！');
            }else{
                $params['pid'] = $data->id;
                $params['level'] = $data->level + 1;
            }
        }else{
            $params['level'] = 1;
            $params['pid'] = 0;
        }
        return $params;
    }

    /**
     * 新增数据前
     * @return array
     */
    protected function toAdding()
    {
        app('db')->beginTransaction();
        try{
            $this->save();
            if(3 === $this->level){
                $list_attributes = request()->input('attribute_id');
                if(is_string($list_attributes)){
                    $list_attributes = explode(',', $list_attributes);
                }
                if(is_array($list_attributes) && !empty($list_attributes)){
                    $data = [];
                    foreach ($list_attributes as $key=>$value){
                        $data[$value] = [
                            'type' => app('attributes_service')->where('id', $value)->value('type')
                        ];
                    }
                    $this->attrs()->attach($data);
                    unset($data);
                }
            }
            unset($list_attributes);
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
     * 修改数据前修改参数
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    protected function toUpdateParams($params)
    {
        if(isset($params['pid'])){
            unset($params['pid']);
        }
        if(isset($params['level'])){
            unset($params['level']);
        }
        return $params;
    }

    /**
     * 修改数据前
     * @return array
     */
    protected function toUpdating()
    {
        $relations = $this->checkRelations($this->id);
        app('db')->beginTransaction();
        try{
            $list_attributes = request()->input('attribute_id');
            if(is_string($list_attributes)){
                $list_attributes = explode(',', $list_attributes);
            }
            if(is_array($list_attributes) && !empty($list_attributes)){
                $list_pivot_attributes = $this->attrs()->pluck('attribute_id')->toArray(); // 中间表已存在属性ID
                if(empty($list_pivot_attributes)){
                    $this->attrs()->attach($list_attributes);
                }else{
                    $detach = array_diff($list_pivot_attributes, $list_attributes);
                    if(!empty($detach)){
                        $this->attrs()->detach($detach); // 删除中间表中未选择的ID
                    }
                    $attach = array_diff($list_attributes, $list_pivot_attributes);
                    if(!empty($attach)){
                        $data = [];
                        foreach ($attach as $key=>$value){
                            $type = app('attributes_service')->where('id', $value)->value('type');
                            if($relations && 2 === $type) continue;
                            $data[$value] = [
                                'type' => $type
                            ];
                            unset($type);
                        }
                        $this->attrs()->attach($data); // 添加中间表中未添加的ID
                        unset($data);
                    }
                    unset($detach, $attach);
                }
                unset($list_pivot_attributes);
            }else{
                $this->attrs()->detach();
            }
            unset($list_attributes);
            $this->save();
            app('db')->commit();
            $result = [
                'code' => 1,
                'message' => '修改成功',
                'data' => $this->getAttributes()
            ];
        }catch (\Exception $exception){
            app('db')->rollBack();
            $result = [
                'code' => 0,
                'message' => '修改失败'
            ];
        }finally{
            unset($relations);
        }
        return $result;
    }



    /**
     * 检测表关联
     */
    public function checkRelations($id)
    {
        $relations = false;
        $data = $this->find($id);
        if(empty($data)){
            return $relations;
        }
        // 检测是否已设置分类
        $relations = $data->spu3()->count() > 0 ? true : false;
//        if(!$relations){
//            $relations = $data->spu2()->count() > 0 ? true : false;
//        }
//        if(!$relations){
//            $relations = $data->spu3()->count() > 0 ? true : false;
//        }
        return $relations;
    }
}
