<?php

namespace Z1px\App\Models\Admins;


use Z1px\App\Models\FilesModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Z1px\App\Models\Model;
use Z1px\App\Rules\MobileRule;
use Z1px\App\Rules\NotEmailRule;
use Z1px\App\Rules\NotMobileRule;
use Z1px\Tool\Format;

class AdminsModel extends Model
{

    use SoftDeletes;

    /**
     * 是否开启数据库表增删改记录
     * @var bool
     */
    protected $tables_operated = true;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'a_admins';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['username', 'nickname', 'mobile', 'email', 'file_id', 'password', 'google_secret', 'access_token', 'status', 'login_failure', 'login_at'];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['status_name', 'avatar'];

    /**
     * 数组中的属性会被隐藏。
     *
     * @var array
     */
    protected $hidden = ['password'];

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

    public function getAvatarAttribute()
    {
        return $this->file_to_image($this->attributes['file_id'], ['width' => 200, 'height' => 200, 'type' => 'fit']);
    }

    /**
     * 定义一个修改器
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        if(empty($value)){
            if(isset($this->attributes['password'])){
                unset($this->attributes['password']);
            }
        }else{
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function setEmailAttribute($value)
    {
        if(empty($value)){
            $this->attributes['email'] = null;
        }else{
            $this->attributes['email'] = Format::format_email($value);
        }
    }

    /**
     * 模型关联，一对一
     * 文件
     */
    public function file()
    {
        return $this->hasOne(app(FilesModel::class), 'file_id');
    }

    /**
     * 模型关联，一对多
     * 登录日志
     */
    public function logins()
    {
        return $this->hasMany(app(AdminsLoginModel::class), 'admin_id');
    }

    /**
     * 模型关联，一对多
     * 行为日志
     */
    public function behaviors()
    {
        return $this->hasMany(app(AdminsBehaviorModel::class), 'admin_id');
    }

    /**
     * 模型关联，多对多
     * 权限
     */
    public function permissions()
    {
        return $this->belongsToMany(app(PermissionsModel::class), app(AdminsPermissionsModel::class)->getTable(), 'admin_id', 'permission_id')
            ->withTimestamps();
    }

    /**
     * 模型关联，多对多
     * 角色
     */
    public function roles()
    {
        return $this->belongsToMany(app(RolesModel::class), app(AdminsRolesModel::class)->getTable(), 'admin_id', 'role_id')
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
                $rules['username'] = [
                    "required",
                    "between:4,20",
                    "alpha_dash",
                    new NotMobileRule(),
                    new NotEmailRule(),
                    "unique:{$this->getTable()},username"
                ];
                $rules['nickname'] = "nullable|between:2,30";
                $rules['mobile'] = [
                    "nullable",
                    new MobileRule(),
                    "unique:{$this->getTable()},mobile"
                ];
                $rules['email'] = "nullable|email:rfc,dns|unique:{$this->getTable()},email";
                $rules['file_id'] = "nullable|integer";
                $rules['password'] = "required|between:6,20";
                $rules['status'] = "in:" . implode(',', array_keys($this->list_status));
                break;
            case 'update':
                $rules['username'] = [
                    "between:4,20",
                    "alpha_dash",
                    new NotMobileRule(),
                    new NotEmailRule(),
                    Rule::unique($this->getTable())->ignore(request()->input('id'))
                ];
                $rules['nickname'] = "nullable|between:2,30";
                $rules['mobile'] = [
                    "nullable",
                    new MobileRule(),
                    Rule::unique($this->getTable())->ignore(request()->input('id'))
                ];
                $rules['email'] = [
                    "nullable",
                    "email:rfc,dns",
                    Rule::unique($this->getTable())->ignore(request()->input('id'))
                ];
                $rules['file_id'] = "nullable|integer";
                $rules['password'] = "nullable|between:6,20";
                $rules['status'] = "in:" . implode(',', array_keys($this->list_status));
                break;
            case 'delete':
                $rules['id'] = "required|integer|not_in:1";
                break;
            case 'login':
                $rules['username'] = "required";
                $rules['password'] = "required";
                break;
            case 'loginInfo':
            case 'logout':
                $rules['access_token'] = "required";
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
            'username' => '用户名',
            'nickname' => '昵称',
            'mobile' => '手机号',
            'email' => '邮箱',
            'avatar' => '头像',
            'file_id' => '头像文件ID',
            'password' => '密码',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
