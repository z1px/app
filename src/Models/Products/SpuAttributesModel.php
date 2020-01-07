<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:46 上午
 */


namespace Z1px\App\Models\Products;


use Z1px\App\Models\FilesModel;
use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class SpuAttributesModel extends Model
{

    use AsPivot;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_spu_attributes';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['spu_id', 'attribute_id', 'spec_id', 'type', 'title'];

    /**
     * 模型关联，一对多（反向）
     * 规格
     */
    public function spec()
    {
        return $this->belongsTo(app(SpecsModel::class), 'spec_id');
    }

}
