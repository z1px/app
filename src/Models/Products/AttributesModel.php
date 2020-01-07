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

class AttributesModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_attributes';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['attributes_group_id', 'title', 'type', 'status'];

    /**
     * 属性类型列表
     * @var array
     */
    public $list_type = [
        1 => '关键属性',
        2 => '销售属性',
        3 => '非关键属性'
    ];

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

    public function getTypeNameAttribute()
    {
        return $this->list_type[$this->attributes['type']] ?? null;
    }

    public function getAttributesGroupNameAttribute()
    {
        return $this->group->title;
    }

    /**
     * 模型关联，一对多
     * 规格
     */
    public function specs()
    {
        return $this->hasMany(app(SpecsModel::class), 'attribute_id');
    }

    /**
     * 模型关联，一对多（反向）
     * 属性分组
     */
    public function group()
    {
        return $this->belongsTo(app(AttributesGroupModel::class), 'attributes_group_id');
    }

    /**
     * 模型关联，多对多
     * 分类
     */
    public function category()
    {
        return $this->belongsToMany(app(CategoryModel::class), app(CategoryAttributesModel::class)->getTable(), 'attribute_id', 'category_id')
            ->withPivot(['type'])
            ->withTimestamps();
    }

    /**
     * 模型关联，多对多
     * 产品信息
     */
    public function spu()
    {
        return $this->belongsToMany(app(SpuModel::class), app(SpuAttributesModel::class)->getTable(), 'attribute_id', 'spu_id')
            ->withPivot(['spec_id', 'title', 'type'])
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
                $rules['attributes_group_id'] = "required|integer";
                $rules['title'] = "required|between:2,20|unique:{$this->getTable()},title";
                $rules['type'] = "required|in:" . implode(',', $this->list_type);
                $rules['status'] = "in:" . implode(',', $this->list_status);
                break;
            case 'update':
                $rules['attributes_group_id'] = "integer";
                $rules['title'] = [
                    "between:2,20",
                    Rule::unique($this->getTable(), 'title')->ignore(request()->input('id'))
                ];
                $rules['type'] = "in:" . implode(',', $this->list_type);
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
            'attributes_group_id' => '属性分组',
            'title' => '属性名称',
            'type' => '属性类型',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
