<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:45 上午
 */


namespace Z1px\App\Http\Services\Products;


use Z1px\App\Models\Products\SpecsModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToListAll;
use Z1px\App\Traits\Eloquent\ToUpdate;

class SpecsService extends SpecsModel
{

    use ToInfo, ToAdd, ToUpdate, ToList, ToListAll;

    /**
     * 新增数据前
     * @return $this
     */
    protected function toAdding()
    {
        $file = app('files_service')->toAdd($this, 'logo', 'brands');
        if(!empty($file)){
            $this->setAttribute('file_id', $file['id']);
        }
        unset($file);
        return $this;
    }

    /**
     * 新增数据后
     * @return $this
     */
    protected function toAdded()
    {
        if($this->file_id){
            app('files_service')->toUpdate($this->file_id, $this->id);
        }
        return $this;
    }


    protected function toUpdateParams(array $params)
    {
        // 禁止修改属性
        if(isset($params['attribute_id'])){
            unset($params['attribute_id']);
        }
        return $params;
    }

    /**
     * 更新数据前
     * @return $this
     */
    protected function toUpdating()
    {
        if(request()->input('logo') && false === filter_var(request()->input('logo'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)){
            $this->setAttribute('file_id', 0);
        }else{
            $file = app('files_service')->toAdd($this, 'logo', 'brands');
            if(!empty($file)){
                $this->setAttribute('file_id', $file['id']);
            }
            unset($file);
        }
        return $this;
    }

    /**
     * 更新数据后
     * @return $this
     */
    protected function toUpdated()
    {
        $before_file_id = $this->getBeforeAttribute('file_id');
        if(!empty($before_file_id)){
            if($this->file_id !== $before_file_id){
                app('files_service')->toInvisible($before_file_id);
            }
        }
        unset($before_file_id);
        return $this;
    }


}
