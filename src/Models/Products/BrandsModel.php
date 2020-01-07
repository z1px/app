<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:44 上午
 */


namespace Z1px\App\Models\Products;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class BrandsModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_brands';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['title', 'file_id', 'pinyin', 'description', 'website', 'sort', 'status'];

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

    public function getLogoAttribute()
    {
        return $this->file_to_image();
    }

    /**
     * 定义一个修改器
     *
     * @param $value
     */
    public function setPinyinAttribute($value)
    {
        if(empty($value)){
            $value = app('pinyin')->permalink($this->attributes['title'], '.');
        }
        $this->attributes['pinyin'] = strtolower($value);
    }

    /**
     * 模型关联，一对多
     * 产品
     */
    public function spu()
    {
        return $this->hasMany(app(SpuModel::class), 'brand_id');
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
//                $rules['pinyin'] = "required";
                $rules['status'] = "in:" . implode(',', $this->list_status);
                break;
            case 'update':
                $rules['title'] = [
                    "between:2,20",
                    Rule::unique($this->getTable(), 'title')->ignore(request()->input('id'))
                ];
//                $rules['pinyin'] = "required";
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
            'title' => '品牌名称',
            'pinyin' => '品牌拼音',
            'description' => '品牌描述',
            'website' => '官网地址',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
