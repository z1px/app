<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:45 上午
 */


namespace Z1px\App\Http\Services\Products;


use Z1px\App\Models\Products\AttributesModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToListAll;
use Z1px\App\Traits\Eloquent\ToUpdate;

class AttributesService extends AttributesModel
{

    use ToInfo, ToAdd, ToUpdate, ToList, ToListAll;


    protected function toListAlling(object $data): object
    {
        $data = $data->orderBy('type', 'asc')->orderBy('id', 'asc');
        return $data;
    }

    protected function toUpdateParams(array $params)
    {
        // 检测依赖关系，已存在依赖关系的，禁止修改类型
        if(isset($params['id']) && !empty($params['id'])){
            if($this->checkRelations($params['id']) && isset($params['type'])){
                unset($params['type']);
            }
        }
        return $params;
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
        $relations = $data->category()->count() > 0 ? true : false;
        if(!$relations){
            // 检测是否已添加规格
            $relations = $data->specs()->count() > 0 ? true : false;
        }
        return $relations;
    }

}
