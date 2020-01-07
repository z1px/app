<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:45 上午
 */


namespace Z1px\App\Models\Products;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class SpecsModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_specs';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['attribute_id', 'title', 'file_id', 'status'];

    /**
     * 状态列表
     * @var array
     */
    public $list_status = [
        1 => '正常',
        2 => '禁用'
    ];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return void
     */
    public function getStatusNameAttribute()
    {
        return $this->list_status[$this->attributes['status']] ?? null;
    }

    public function getAttributeNameAttribute()
    {
        return $this->attr->title;
    }

    public function getLogoAttribute()
    {
        return $this->file_to_image();
    }

    /**
     * 模型关联，一对多（反向）
     * 属性
     */
    public function attr()
    {
        return $this->belongsTo(app(AttributesModel::class), 'attribute_id');
    }

    /**
     * 模型关联，多对多
     * 产品信息
     */
    public function spu()
    {
        return $this->belongsToMany(app(SpuModel::class), app(SpuSpecsModel::class)->getTable(), 'spec_id', 'spu_id')
            ->withPivot(['attribute_id', 'title'])
            ->withTimestamps();
    }

    /**
     * 模型关联，多对多
     * 产品库存
     */
    public function sku()
    {
        return $this->belongsToMany(app(SkuModel::class), app(SkuSpecsModel::class)->getTable(), 'spec_id', 'sku_id')
            ->withPivot(['attribute_id', 'title', 'file_id'])
            ->withTimestamps();
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
            case 'add':
                $rules['attribute_id'] = "required|integer";
                $rules['title'] = "required|between:1,20|unique:{$this->getTable()},title";
                $rules['status'] = "in:" . implode(',', $this->list_status);
                break;
            case 'update':
                $rules['attribute_id'] = "integer";
                $rules['title'] = [
                    "between:1,20",
                    Rule::unique($this->getTable(), 'title')->ignore(request()->input('id'))
                ];
                $rules['status'] = "in:" . implode(',', $this->list_status);
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
            'attribute_id' => '属性',
            'title' => '规格名称',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
