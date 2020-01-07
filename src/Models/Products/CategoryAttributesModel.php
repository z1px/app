<?php

namespace Z1px\App\Models\Products;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class CategoryAttributesModel extends Model
{

    use AsPivot;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_category_attributes';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['category_id', 'attribute_id', 'type'];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return void
     */
    public function getTitleAttribute()
    {
        return $this->attr->title;
    }

    /**
     * 模型关联，一对多
     * 规格
     */
    public function attr()
    {
        return $this->belongsTo(app(AttributesModel::class), 'attribute_id');
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
            case 'update':
                $rules['category_id'] = "required|integer";
                $rules['attribute_id'] = "required|integer";
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
            'category_id' => '分类ID',
            'attribute_id' => '属性ID',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
