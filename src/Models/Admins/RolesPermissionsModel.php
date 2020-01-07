<?php

namespace Z1px\App\Models\Admins;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

class RolesPermissionsModel extends Model
{

    use AsPivot;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'a_roles_permissions';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['role_id', 'permission_id'];

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
            case 'update':
                $rules['role_id'] = "required|integer";
                $rules['permission_id'] = "required|integer";
                break;
        }
        return $rules;
    }

    /**
     * 获取验证错误的自定义属性。
     *
     * @return array
     */
    public function attributes($key=null)
    {
        $attributes = array_merge(parent::attributes(), [
            'role_id' => '角色ID',
            'permission_id' => '权限ID',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
