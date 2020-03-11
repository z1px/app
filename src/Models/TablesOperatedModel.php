<?php

namespace Z1px\App\Models;


use Z1px\App\Http\Services\Admins\AdminsService;
use Z1px\App\Http\Services\Users\UsersService;

class TablesOperatedModel extends Model
{

    /**
     * 是否开启数据库表增删改记录
     * @var bool
     */
    protected $tables_operated = false;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'l_tables_operated';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['model', 'table', 'tid', 'operate', 'before_attr', 'after_attr', 'change_attr', 'route_name', 'url', 'ip', 'area', 'platform', 'user_type', 'user_id'];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['table_comment', 'operate_name', 'user_type_name', 'user'];

    /**
     * 这个属性应该被转换为原生类型.
     *
     * @var array
     */
    protected $casts = [
        'before_attr' => 'array',
        'after_attr' => 'array',
        'change_attr' => 'array',
    ];

    /**
     * 用户操作列表
     * @var array
     */
    public $list_operate = [
        'create' => '新增',
        'delete' => '删除',
        'update' => '修改',
        'select' => '查找',
        'restore' => '恢复',
    ];

    /**
     * 用户类型列表
     * @var array
     */
    public $list_user_type = [
        1 => '管理员',
        2 => '平台用户',
    ];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return string
     */
    public function getTableCommentAttribute()
    {
        try{
            $options = app('db')->getDoctrineSchemaManager()->listTableDetails($this->table)->getOptions();
            $value = $options['comment'];
            unset($options);
        }catch (\Exception $exception){
            $value = '';
        }
        return $value;
    }

    public function getOperateNameAttribute()
    {
        return $this->list_operate[$this->operate] ?? '未知';
    }

    public function getUserTypeNameAttribute()
    {
        return $this->list_user_type[$this->user_type] ?? '未知';
    }

    public function getUserAttribute()
    {
        if($this->user_id > 0){
            switch ($this->user_type){
                case 1:
                    $value = app(AdminsService::class)->toInfo($this->user_id);
                    break;
                case 2:
                    $value = app(UsersService::class)->toInfo($this->user_id);
                    break;
                default:
                    $value = '';
            }
        }else{
            $value = '';
        }
        return $value;
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
            case 'update':
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
            'model' => '操作表模型',
            'table' => '操作表名称',
            'tid' => '操作表ID',
            'operate' => '操作类型',
            'before_attr' => '操作前的数据',
            'after_attr' => '操作后的数据',
            'change_attr' => '被修改的数据',
            'user_id' => '文件创建者用户ID',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}

