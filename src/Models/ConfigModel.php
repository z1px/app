<?php

namespace Z1px\App\Models;


use Illuminate\Validation\Rule;

class ConfigModel extends Model
{

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'config';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['title', 'key', 'value', 'description', 'input', 'values', 'type', 'status'];

    /**
     * 这个属性应该被转换为原生类型.
     *
     * @var array
     */
    protected $casts = [
        'values' => 'array'
    ];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['status_name', 'input_name', 'type_name'];

    /**
     * 表单操作类型
     * @var array
     */
    public $list_input = [
        'text' => '文本',
        'select' => '下拉框',
        'radio' => '单选框',
        'checkbox' => '复选框',
    ];

    /**
     * 配置类型
     * @var array
     */
    public $list_type = [
        1 => '基本配置',
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
    public function getInputNameAttribute()
    {
        return $this->list_input[$this->attributes['input']] ?? null;
    }

    public function getTypeNameAttribute()
    {
        return $this->list_type[$this->attributes['type']] ?? null;
    }

    public function getStatusNameAttribute()
    {
        return $this->list_status[$this->attributes['status']] ?? null;
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
                $rules['title'] = "required|between:2,30|unique:{$this->getTable()},title";
                $rules['key'] = [
                    "required",
                    "between:2,30",
                    "alpha_dash",
                    "unique:{$this->getTable()},key"
                ];
                $rules['value'] = "between:0,120";
                $rules['description'] = "between:0,200";
                $rules['input'] = "required|in:" . implode(',', array_keys($this->list_input));
                $rules['type'] = "required|integer|in:" . implode(',', array_keys($this->list_type));
                $rules['status'] = "in:" . implode(',', array_keys($this->list_status));
                break;
            case 'update':
                $rules['title'] = [
                    "required",
                    "between:2,30",
                    Rule::unique($this->getTable())->ignore(request()->input('id'))
                ];
                $rules['key'] = [
                    "required",
                    "between:2,30",
                    "alpha_dash",
                    Rule::unique($this->getTable())->ignore(request()->input('id'))
                ];
                $rules['value'] = "between:0,120";
                $rules['description'] = "between:0,200";
                $rules['input'] = "required|in:" . implode(',', array_keys($this->list_input));
                $rules['type'] = "required|integer|in:" . implode(',', array_keys($this->list_type));
                $rules['status'] = "in:" . implode(',', array_keys($this->list_status));
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
            'title' => '标题',
            'key' => '配置健',
            'value' => '配置值',
            'description' => '配置描述',
            'input' => '表单操作类型',
            'values' => '默认可选值',
            'type' => '配置类型',
            'status' => '状态',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}

