<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/9/29
 * Time: 6:17 下午
 */


namespace Z1px\App\Http\Services\Users;


use Z1px\App\Models\Users\UsersModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class UsersService extends UsersModel
{

    use ToAdd, ToUpdate, ToInfo, ToDelete, ToList, ToRestore;

    /**
     * 新增数据前
     * @return $this
     */
    protected function toAdding()
    {
        $file = app('files_service')->toAdd($this, 'avatar', 'users/avatars');
        if(!empty($file)){
            $this->setAttribute('file_id', $file['id']);
        }
        unset($file);
        return $this;
    }

    /**
     * 新增数据后
     * @return bool
     */
    protected function toAdded()
    {
        if($this->file_id){
            app('files_service')->toUpdate($this->file_id, $this->id);
        }
        return true;
    }

    /**
     * 更新数据前
     * @return bool
     */
    protected function toUpdating()
    {
        if(request()->input('avatar') && false === filter_var(request()->input('avatar'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)){
            $this->setAttribute('file_id', 0);
        }else{
            $file = app('files_service')->toAdd($this, 'avatar', 'users/avatars');
            if(!empty($file)){
                $this->setAttribute('file_id', $file['id']);
            }
            unset($file);
        }
        return true;
    }

    /**
     * 更新数据后
     * @return bool
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
        return true;
    }

}
