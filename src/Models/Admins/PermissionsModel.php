<?php

namespace Z1px\App\Models\Admins;


use Illuminate\Validation\Rule;
use Z1px\App\Models\Model;

class PermissionsModel extends Model
{

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'm_permissions';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['title', 'route_name', 'description', 'status', 'pid'];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['status_name', 'pname'];

    /**
     * 状态列表
     * @var array
     */
    public $list_status = [
        1 => '正常',
        2 => '禁用'
    ];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return void
     */
    public function getStatusNameAttribute()
    {
        return $this->list_status[$this->attributes['status']] ?? null;
    }

    public function getPnameAttribute()
    {
        return $this->attributes['pid'] > 0 ? $this->parent->title : '';
    }

    /**
     * 模型关联，一对多
     * 子级菜单
     */
    public function children()
    {
        return $this->hasMany(app(self::class), 'pid');
    }

    /**
     * 模型关联，一对多（反向）
     * 父级菜单
     */
    public function parent()
    {
        return $this->belongsTo(app(self::class), 'pid');
    }

    /**
     * 模型关联，多对多
     * 角色
     */
    public function roles()
    {
        return $this->belongsToMany(app(RolesModel::class), app(RolesPermissionsModel::class)->getTable(), 'permission_id', 'role_id')
            ->withTimestamps();
    }

    /**
     * 模型关联，多对多
     * 管理员
     */
    public function admins()
    {
        return $this->belongsToMany(app(AdminsModel::class), app(AdminsPermissionsModel::class)->getTable(), 'permission_id', 'admin_id')
            ->withTimestamps();
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
                $rules['title'] = "required|between:2,30|unique:{$this->getTable()},title";
                $rules['route_name'] = "required|unique:{$this->getTable()},route_name";
                $rules['status'] = "in:" . implode(',', array_keys($this->list_status));
                break;
            case 'update':
                $rules['title'] = [
                    "between:2,30",
                    Rule::unique($this->getTable())->ignore(request()->input('id'))
                ];
                $rules['route_name'] = [
                    Rule::unique($this->getTable())->ignore(request()->input('id'))
                ];
                $rules['status'] = "in:" . implode(',', array_keys($this->list_status));
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
            'title' => '权限名称',
            'route_name' => '路由名称',
            'description' => '角色描述',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

    /**
     * 通过route别名查找route方法
     */
    public function getRouteActionByRouteName($route_name)
    {
        $action = app('router')->getRoutes()->getByName($route_name);
        if(empty($action)){
            $route_action = '';
        }else{
            $route_action = $action->getActionName();
        }
        unset($action);
        return $route_action;
    }

}
