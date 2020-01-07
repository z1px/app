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
     * 这个属性应该被转换为原生类型.
     *
     * @var array
     */
    protected $casts = [
        'header' => 'array',
        'request' => 'array',
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
