<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/5
 * Time: 9:43 上午
 */


namespace Z1px\App\Models\Users;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersPassportsModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'u_users_passports';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['user_id', 'access_token', 'route_name', 'url', 'ip', 'area', 'platform', 'model'];

    /**
     * 模型关联，一对多（反向）
     * 用户
     */
    public function users()
    {
        return $this->belongsTo(app(UsersModel::class), 'user_id');
    }

}
