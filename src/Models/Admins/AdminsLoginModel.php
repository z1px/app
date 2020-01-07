<?php

namespace Z1px\App\Models\Admins;


use Z1px\App\Models\Model;

class AdminsLoginModel extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'a_admins_login';

    /**
     * 应该被转换成原生类型的属性。
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'timestamp',
    ];

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['admin_id', 'nickname', 'username', 'mobile', 'email', 'route_name', 'route_action', 'url',
        'method', 'ip', 'area', 'user_agent', 'device'];

    /**
     * 模型关联，一对多（反向）
     * 管理员
     */
    public function admin()
    {
        return $this->belongsTo(app(AdminsModel::class), 'admin_id');
    }

}

