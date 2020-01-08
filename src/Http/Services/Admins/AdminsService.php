<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/9/29
 * Time: 6:17 下午
 */


namespace Z1px\App\Http\Services\Admins;


use Z1px\App\Http\Services\FilesService;
use Z1px\App\Models\Admins\AdminsModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class AdminsService extends AdminsModel
{

    use ToAdd, ToUpdate, ToInfo, ToDelete, ToRestore, ToList;

    private $files_model = FilesService::class;

    /**
     * 新增数据前
     * @return $this
     */
    protected function toAdding()
    {
        $file = app($this->files_model)->toAdd($this, 'avatar', 'avatars');
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
            app($this->files_model)->toUpdate($this->file_id, $this->id);
        }
        return $this;
    }

    /**
     * 更新数据前
     * @return $this
     */
    protected function toUpdating()
    {
        if(request()->input('avatar') && false === filter_var(request()->input('avatar'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)){
            $this->setAttribute('file_id', 0);
        }else{
            $file = app($this->files_model)->toAdd($this, 'avatar', 'avatars');
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
                app($this->files_model)->toInvisible($before_file_id);
            }
        }
        unset($before_file_id);
        return $this;
    }

}
