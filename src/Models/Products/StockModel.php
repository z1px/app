<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:47 上午
 */


namespace Z1px\App\Models\Products;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockModel extends Model
{

    use SoftDeletes;

    /**
     * 是否开启数据库表增删改记录
     * @var bool
     */
    protected $tables_operated = true;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_stock';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['spu_id', 'sku_id', 'sn', 'price', 'stock', 'type', 'remark'];

    /**
     * 进销类型
     * @var array
     */
    public $list_type = [
        1 => '进货',
        2 => '退货'
    ];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return void
     */
    public function getTypeNameAttribute()
    {
        return $this->list_type[$this->attributes['type']] ?? null;
    }

    public function getTitleAttribute()
    {
        return $this->sku->title;
    }

    /**
     * 模型关联，一对多（反向）
     * 品牌
     */
    public function spu()
    {
        return $this->belongsTo(app(SpuModel::class), 'spu_id');
    }

    /**
     * 模型关联，一对多（反向）
     * 库存
     */
    public function sku()
    {
        return $this->belongsTo(app(SkuModel::class), 'sku_id');
    }

    /**
     * 获取适用于请求的验证规则
     *
     * @param $scene 验证场景
     * @return array
     */
    public function rules($scene='update')
    {
        $rules = parent::rules($scene);
        switch ($scene){
            case 'list':
                $rules['sku_id'] = "required|integer";
                break;
            case 'in':
            case 'out':
                $rules['sku_id'] = "required|integer";
                $rules['price'] = "required|numeric|gte:0";
                $rules['stock'] = "required|integer|gt:0";
                $rules['type'] = "filled|in:" . implode(',', $this->list_type);
                $rules['remark'] = "between:0,200";
                break;
        }
        return $rules;
    }

    /**
     * 获取验证错误的自定义属性。
     *
     * @return array
     */
    public function attributes($key=null)
    {
        $attributes = array_merge(parent::attributes(), [
            'spu_id' => '产品信息ID',
            'sku_id' => '产品库存ID',
            'sn' => '产品序列号',
            'price' => '进销价',
            'stock' => '库存',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
