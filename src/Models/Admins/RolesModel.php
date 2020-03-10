<?php

namespace Z1px\App\Models\Admins;


use Illuminate\Validation\Rule;
use Z1px\App\Models\Model;

class RolesModel extends Model
{

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'm_roles';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['title', 'brief', 'status'];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['status_name'];

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

    /**
     * 模型关联，多对多
     * 权限
     */
    public function permissions()
    {
        return $this->belongsToMany(app(PermissionsModel::class), app(RolesPermissionsModel::class)->getTable(), 'role_id', 'permission_id')
            ->withTimestamps();
    }

    /**
     * 模型关联，多对多
     * 管理员
     */
    public function admins()
    {
        return $this->belongsToMany(app(AdminsModel::class), app(AdminsRolesModel::class)->getTable(), 'role_id', 'admin_id')
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
                $rules['title'] = "required|between:2,20|unique:{$this->getTable()},title";
                $rules['status'] = "in:" . implode(',', array_keys($this->list_status));
                break;
            case 'update':
                $rules['title'] = [
                    "between:2,20",
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
            'title' => '角色名称',
            'brief' => '角色简介',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }


}
