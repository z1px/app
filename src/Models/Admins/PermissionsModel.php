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
    protected $table = 'a_permissions';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['title', 'route_name', 'route_action', 'icon', 'sort', 'type', 'show', 'status', 'pid'];

    /**
     * 展示列表
     * @var array
     */
    public $list_show = [
        1 => '展示',
        2 => '隐藏'
    ];

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

    public function getShowNameAttribute()
    {
        return $this->list_show[$this->attributes['show']] ?? null;
    }

    /**
     * 定义一个修改器
     *
     * @param  string  $value
     * @return void
     */
    public function setRouteActionAttribute($value)
    {
        $this->attributes['route_action'] = $value;
        if(empty($value)){
            $this->attributes['route_action'] = $this->getRouteActionByRouteName($this->attributes['route_name']) ?: $value;
        }
    }

    public function setIconAttribute($value){
        $this->attributes['icon'] = $value;
        if(false !== strpos($value, 'fa-') && false === strpos($value, 'fa ')){
            $this->attributes['icon'] = "fa {$value}";
        }
        if(false !== strpos($value, 'glyphicon-') && false === strpos($value, 'glyphicon ')){
            $this->attributes['icon'] = "glyphicon {$value}";
        }
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
                $rules['route_action'] = "required|nullable|unique:{$this->getTable()},route_action";
                $rules['icon'] = "between:0,60";
                $rules['sort'] = "integer";
                $rules['type'] = "integer";
                $rules['show'] = "integer";
                $rules['status'] = "in:" . implode(',', $this->list_status);
                break;
            case 'update':
                $rules['title'] = [
                    "between:2,30",
                    Rule::unique($this->getTable(), 'title')->ignore(request()->input('id'))
                ];
                $rules['route_name'] = [
                    Rule::unique($this->getTable(), 'route_name')->ignore(request()->input('id'))
                ];
                $rules['route_action'] = [
                    "nullable",
                    Rule::unique($this->getTable(), 'route_action')->ignore(request()->input('id'))
                ];
                $rules['icon'] = "between:0,60";
                $rules['sort'] = "integer";
                $rules['type'] = "integer";
                $rules['show'] = "integer";
                $rules['status'] = "in:" . implode(',', $this->list_status);
                break;
            case 'drop':
                $rules['id'] = "required|integer";
                $rules['move'] = "required|in:inner,prev,next";
                $rules['tid'] = "required|integer";
                $rules['pid'] = "required|integer";
                $rules['sort'] = "required|integer";
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
            'route_action' => '路由方法',
            'icon' => '图标',
            'sort' => '排序',
            'show' => '是否展示',
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
