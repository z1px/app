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
     * 应该被转换成原生类型的属性。
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'timestamp',
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

