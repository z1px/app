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

class CategoryModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_category';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['title', 'status', 'pid'];

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

    public function getParentTitleAttribute()
    {
        return $this->attributes['pid'] > 0 ? $this->parent->title : "<span class='text-danger'>【顶级菜单】</span>";
    }

    public function top_spu()
    {
        return $this->hasMany(app(SpuModel::class), 'category_pid');
    }

    public function spu()
    {
        return $this->hasMany(app(SpuModel::class), 'category_id');
    }

    /**
     * 模型关联，一对多
     * 子级菜单
     */
    public function children()
    {
        return $this->hasMany(app(self::class), 'pid');
    }

    /**
     * 模型关联，一对多（反向）
     * 父级菜单
     */
    public function parent()
    {
        return $this->belongsTo(app(self::class), 'pid');
    }

    /**
     * 模型关联，多对多
     * 属性
     */
    public function attrs()
    {
        return $this->belongsToMany(app(AttributesModel::class), app(CategoryAttributesModel::class)->getTable(), 'category_id', 'attribute_id')
            ->withPivot(['type'])
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
                $rules['title'] = "required|between:2,20|unique:{$this->getTable()},title";
                $rules['level'] = "in:1,2,3";
                $rules['status'] = "in:" . implode(',', $this->list_status);
                break;
            case 'update':
                $rules['title'] = [
                    "between:2,20",
                    Rule::unique($this->getTable(), 'title')->ignore(request()->input('id'))
                ];
                $rules['level'] = "in:1,2,3";
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
            'title' => '分类名称',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
