<?php

namespace Z1px\App\Models\Users;


use Z1px\App\Models\Model;

class UsersLoginModel extends Model
{

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'u_users_login';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['user_id', 'nickname', 'username', 'mobile', 'email', 'route_name', 'url', 'params', 'ip', 'area', 'platform', 'model', 'brand', 'system'];

    /**
     * 这个属性应该被转换为原生类型.
     *
     * @var array
     */
    protected $casts = [
        'params' => 'array'
    ];

    /**
     * 模型关联，一对多（反向）
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(app(UsersModel::class), 'user_id');
    }

}

