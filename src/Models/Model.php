<?php

namespace Z1px\App\Models;


use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Arr;
use Z1px\App\Http\Services\FilesService;
use Z1px\App\Http\Services\TablesOperatedService;
use Z1px\App\Traits\Eloquent\ToColumnsComment;
use Z1px\App\Traits\Eloquent\ToTableComment;

class Model extends BaseModel
{
    /**
     * 自动完成包含新增操作
     * @var array
     */
    protected $insert = [];

    /**
     * 只读字段用来保护某些特殊的字段值不被更改，这个字段的值一旦写入，就无法更改
     * @var array
     */
    protected $readonly = [];

    /**
     * 是否开启数据库表增删改记录
     * @var bool
     */
    protected $tables_operated = false;
    /**
     * 模型增删改查之前的数据
     * @var array
     */
    protected $before_attributes = [];

    use ToTableComment, ToColumnsComment;

    // 设置模型增删改查之前的数据
    public function setBeforeAttributes($attributes): object
    {
        $this->before_attributes = $attributes;
        return $this;
    }
    public function setBeforeAttribute($key, $value): object
    {
        $this->before_attributes[$key] = $value;
        return $this;
    }

    // 获取模型增删改查之前的数据
    public function getBeforeAttributes()
    {
        return $this->before_attributes;
    }
    // 获取模型增删改查之前的数据
    public function getBeforeAttribute($key)
    {
        return $this->before_attributes[$key] ?? null;
    }

    /**
     * 文件ID转图片地址
     * @param array $data
     * @return string
     */
    public function file_to_image($id = null, array $data = [])
    {
        !is_null($id) || $id = $this->attributes['file_id'];
        if(empty($id)){
            $value = '';
        }else{
            if(app(FilesService::class)->toInfo($id)){
                $data['sn'] = encrypt($id);
                $value = route('files.image', $data);
            }else{
                $value = '';
            }
        }
        unset($id, $data);
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
        $rules = [];
        switch ($scene){
            case 'info':
            case 'update':
            case 'delete':
            case 'restore':
                $rules['id'] = "required|integer";
                break;
        }
        return $rules;
    }

    /**
     * 获取已定义验证规则的错误消息。
     *
     * @return array
     */
    public function messages($scene=null)
    {
        $messages = [
            'regex' => ':attribute 格式错误',
        ];
        return $messages;
    }

    /**
     * 获取验证错误的自定义属性。
     *
     * @return array
     */
    public function attributes($key=null)
    {
        $attributes = [
            'id' => 'ID',
            'file_id' => '文件ID',
            'type' => '类型',
            'status' => '状态',
            'route_name' => '路由名称',
            'route_action' => '路由方法',
            'url' => '请求地址',
            'method' => '请求类型',
            'ip' => '请求IP',
            'area' => 'IP区域',
            'user_type' => '用户类型',
            'user_id' => '用户ID',
            'admin_id' => '管理员ID',
            'logo' => 'LOGO',
            'remark' => '备注',
            'start_date' => '开始时间',
            'end_date' => '结束时间',
        ];
        if(is_null($key)){
            return $attributes;
        }else{
            if(!isset($attributes[$key])){
                try{
                    $columns = $this->toColumnsComment();
                    $attributes[$key] = $columns->getColumn($key)->getComment();
                    !empty($attributes[$key]) || $attributes[$key] = $key;
                }catch (\Exception $exception){

                }
            }
            return $attributes[$key] ?? $key;
        }
    }

    /**
     * 查询条件构造
     * @param $data
     * @param array $params
     * @return mixed
     */
    protected function toWhere(object $data, array $params): object
    {
        if(!empty($params)){
            foreach ($params as $key=>$value){
                if(empty($value)) continue;
                switch ($key){
                    case 'title':
                        $data = $data->where($key, 'like', "%{$value}%");
                        break;
                    case 'start_date':
                        $data = $data->whereDate('created_at', '>=', $value);
                        break;
                    case 'end_date':
                        $data = $data->whereDate('created_at', '<=', $value);
                        break;
                    case 'date_range':
                        list($start_date, $end_date) = $value;
                        $data = $data->whereDate('created_at', '>=', $start_date)
                            ->whereDate('created_at', '<=', $end_date);
                        unset($start_date, $end_date);
                        break;
                    default:
                        if($this->isFillable($key)){
                            if(is_array($value)){
                                $data = $data->whereIn($key, $value);
                            }else{
                                $data = $data->where($key, $value);
                            }
                        }
                }
            }
        }
        return $data;
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::creating(function ($model){
            if(is_array($model->insert) && !empty($model->insert)){
                $attributes = $model->getAttributes();
                foreach ($model->insert as $value){
                    if(is_array($value)){
                        $key = $value;
                        $value = $value[$key];
                    }else{
                        $key = $value;
                        $value = null;
                    }
                    if(!array_key_exists($key, $attributes)){
                        $model->setAttribute($key, $value);
                    }
                    unset($key, $value);
                }
                unset($attributes);
            }
        });

        static::updating(function ($model){
            if(is_array($model->readonly) && !empty($model->readonly)){
                $model->setRawAttributes(Arr::except($model->getAttributes(), $model->readonly));
            }
        });

        $tables_operated_model = TablesOperatedService::class;

        static::created(function ($model) use ($tables_operated_model) {
            if(isset($model->tables_operated) && true === $model->tables_operated) {
                app($tables_operated_model)->toAdd($model, 'create');
            }
        });
        static::updated(function ($model) use ($tables_operated_model) {
            if(isset($model->tables_operated) && true === $model->tables_operated && $model->wasChanged()) {
                app($tables_operated_model)->toAdd($model, 'update');
            }
        });
        static::deleted(function ($model) use ($tables_operated_model) {
            if(isset($model->tables_operated) && true === $model->tables_operated) {
                app($tables_operated_model)->toAdd($model, 'delete');
            }
        });
        if(method_exists(static::class, 'restored')){
            static::restored(function ($model) use ($tables_operated_model) {
                if(isset($model->tables_operated) && true === $model->tables_operated) {
                    app($tables_operated_model)->toAdd($model, 'restore');
                }
            });
        }

        unset($tables_operated_model);

    }
}
