<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/8
 * Time: 8:49 上午
 */


namespace Z1px\App\Models\Products;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class ServicesModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'p_services';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['title', 'file_id', 'description', 'status'];

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
     * 模型关联，多对多
     * 产品信息
     */
    public function spu()
    {
        return $this->belongsToMany(app(SpuModel::class), app(SpuServicesModel::class)->getTable(), 'service_id', 'spu_id')
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
                $rules['description'] = "required";
                $rules['status'] = "in:" . implode(',', $this->list_status);
                break;
            case 'update':
                $rules['title'] = [
                    "between:2,20",
                    Rule::unique($this->getTable(), 'title')->ignore(request()->input('id'))
                ];
                $rules['description'] = "required";
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
            'title' => '服务名称',
            'description' => '服务描述',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
