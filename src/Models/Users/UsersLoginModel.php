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
    protected $fillable = ['user_id', 'nickname', 'username', 'mobile', 'email', 'route_name', 'url', 'ip', 'area', 'device'];

    /**
     * 模型关联，一对多（反向）
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(app(UsersModel::class), 'user_id');
    }

}

