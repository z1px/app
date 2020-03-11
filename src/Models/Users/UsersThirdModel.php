<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2020/3/4
 * Time: 1:35 上午
 */


namespace Z1px\App\Models\Users;


use Z1px\App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersThirdModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'u_users_third';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['unionid', 'openid', 'session_key', 'type', 'user_id'];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['type_name'];

    /**
     * 账号类型
     * @var array
     */
    public $list_type = [
        1 => '微信',
    ];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return void
     */
    public function getTypeNameAttribute()
    {
        return $this->list_type[$this->type] ?? null;
    }

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
                $rules['unionid'] = "nullable|max:100";
                $rules['openid'] = "required|between:2,100";
                $rules['session_key'] = "required|between:2,100";
                $rules['type'] = "in:" . implode(',', array_keys($this->list_type));
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
            'unionid' => '用户在开放平台的唯一标识符',
            'openid' => '用户唯一标识',
            'session_key' => '会话密钥',
            'type' => '账号类型',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}
