<?php

namespace Z1px\App\Models\Users;


use Z1px\App\Models\Model;

class UsersBehaviorModel extends Model
{

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'l_users_behavior';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['title', 'route_name', 'url', 'params', 'ip', 'area', 'platform', 'model', 'runtime', 'user_id'];

    /**
     * 这个属性应该被转换为原生类型.
     *
     * @var array
     */
    protected $casts = [
        'params' => 'array'
    ];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['username'];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return void
     */
    public function getUsernameAttribute()
    {
        return $this->user_id > 0 ? $this->user->username : '';
    }

    /**
     * 模型关联，一对多（反向）
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(app(UsersModel::class), 'user_id');
    }

    /**
     * 获取验证错误的自定义属性。
     *
     * @return array
     */
    public function attributes($key=null)
    {
        $attributes = array_merge(parent::attributes(), [
            'title' => '行为名称',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
