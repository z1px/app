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
    protected $table = 'l_admins_login';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['nickname', 'username', 'mobile', 'email', 'route_name', 'url', 'params', 'ip', 'area', 'platform', 'model', 'admin_id'];

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
     * 管理员
     */
    public function admin()
    {
        return $this->belongsTo(app(AdminsModel::class), 'admin_id');
    }

}

