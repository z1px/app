<?php

namespace Z1px\App\Models\Admins;


use Z1px\App\Models\Model;

class AdminsBehaviorModel extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'a_admins_behavior';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['admin_id', 'title', 'route_name', 'route_action', 'url', 'method', 'header', 'request',
        'response', 'ip', 'area', 'user_agent', 'device', 'runtime'];

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
     * 管理员
     */
    public function admin()
    {
        return $this->belongsTo(app(AdminsModel::class), 'admin_id');
    }

}
