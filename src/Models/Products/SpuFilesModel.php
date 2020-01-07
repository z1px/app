<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:46 上午
 */


namespace Z1px\App\Models\Products;


use Z1px\App\Models\Model;

class SpuFilesModel extends Model
{

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_spu_files';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['spu_id', 'file_id'];

    /**
     * 定义一个访问器
     * @return string
     */
    public function getImageAttribute()
    {
        return $this->file_to_image();
    }

    /**
     * 模型关联，一对多（反向）
     * 产品信息
     */
    public function images()
    {
        return $this->belongsTo(app(SpuModel::class), 'spu_id');
    }

}
