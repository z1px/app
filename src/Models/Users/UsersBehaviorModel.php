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
    protected $table = 'u_users_behavior';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['user_id', 'title', 'route_name', 'url', 'params', 'ip', 'area', 'platform', 'runtime'];

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
        return $this->attributes['admin_id'] > 0 ? $this->user->username : '';
    }

    /**
     * 模型关联，一对多（反向）
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(app(UsersModel::class), 'user_id');
    }
}
