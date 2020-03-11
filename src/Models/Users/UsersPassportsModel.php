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
    protected $fillable = ['access_token', 'route_name', 'url', 'ip', 'area', 'platform', 'model', 'uuid', 'user_id'];

    /**
     * 模型关联，一对多（反向）
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(app(UsersModel::class), 'user_id');
    }


    /**
     * 获取适用于请求的验证规则
     *
     * @param $scene 验证场景
     * @return array
     */
    public function rules($scene='update')
    {
        $rules = parent::rules($scene);
        switch ($scene){
            case 'add':
                $rules['access_token'] = "required|between:2,100";
                $rules['route_name'] = "required|between:2,50";
                $rules['url'] = "required|url";
                $rules['ip'] = "required|ip";
                $rules['area'] = "max:50";
                $rules['platform'] = "max:30";
                $rules['model'] = "max:30";
                $rules['uuid'] = "required|max:32";
                $rules['user_id'] = "required|integer";
                break;
        }
        return $rules;
    }

}
