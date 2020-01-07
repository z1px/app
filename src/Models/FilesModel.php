<?php

namespace Z1px\App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class FilesModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'files';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['model', 'table', 'tid', 'original_name', 'disk', 'visibility', 'path_name', 'file_type', 'extension',
        'mime', 'md5', 'sha1', 'route_name', 'route_action', 'url', 'method', 'ip', 'area', 'user_type', 'user_id',
        'admin_id'];

    /**
     * 文件类型列表
     * @var array
     */
//    public $list_file_type = [
//        1 => 'image', // 图片文件类型
//        2 => 'audio', // 音频文件类型
//        3 => 'video', // 视频文件类型
//        4 => 'text', // 文本文件类型
//        5 => 'application', // 应用文件类型
//        6 => 'archive', // 归档文件类型
//    ];
    public $list_file_type = [
        1 => '图片', // 图片文件类型
        2 => '音频', // 音频文件类型
        3 => '视频', // 视频文件类型
        4 => '文本', // 文本文件类型
        5 => '应用', // 应用文件类型
        6 => '归档', // 归档文件类型
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
     * 文件可见性列表
     * @var array
     */
    public $list_visibility = [
        'public' => '可见',
        'private' => '不可见',
    ];

    /**
     * 定义一个访问器
     *
     * @param  string  $value
     * @return string
     */
    public function getFileTypeNameAttribute()
    {
        return $this->list_file_type[$this->attributes['file_type']] ?? '未知';
    }

    public function getVisibilityNameAttribute()
    {
        return $this->list_visibility[$this->attributes['visibility']] ?? '未知';
    }

    public function getTableCommentAttribute()
    {
        try{
            $options = app('db')->getDoctrineSchemaManager()->listTableDetails($this->attributes['table'])->getOptions();
            $value = $options['comment'];
            unset($options);
        }catch (\Exception $exception){
            $value = '';
        }
        return $value;
    }

    public function getSizeFormatAttribute()
    {
        $value = $this->attributes['size'];
        $unit = 'B';
        if($value > 1024){
            $value /= 1024;
            $unit = 'kB';
        }
        if($value > 1024){
            $value /= 1024;
            $unit = 'MB';
        }
        if($value > 1024){
            $value /= 1024;
            $unit = 'GB';
        }
        if($value > 1024){
            $value /= 1024;
            $unit = 'TB';
        }
        return sprintf("%0.2f %s", $value, $unit);
    }

    public function getUserTypeNameAttribute()
    {
        return $this->list_user_type[$this->attributes['user_type']] ?? '未知';
    }

    public function getBase64Attribute()
    {
        if(Storage::disk($this->attributes['disk'])->exists($this->attributes['path_name'])
            && 1 === ($this->attributes['file_type']) && !empty($this->attributes['extension'])){
            $value = "data:image/{$this->attributes['extension']};base64," . chunk_split(base64_encode(Storage::disk($this->attributes['disk'])->get($this->attributes['path_name']))); // 合成图片的base64编码;
        }else{
            $value = '';
        }
        return $value;
    }

    public function getImageAttribute()
    {
        if(1 === $this->attributes['type']){
            $value = $this->file_to_image($this->attributes['id']);
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
                $rules['disk'] = 'required|in:public,local';
                $rules['visibility'] = 'required|in:public,private';
                break;
            case 'update':
                $rules['disk'] = 'in:public,local';
                $rules['visibility'] = 'in:public,private';
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
            'model' => '关联表模型',
            'table' => '关联表名称',
            'tid' => '关联表ID',
            'original_name' => '文件原始名称',
            'disk' => '文件存储磁盘名称',
            'visibility' => '文件可见性',
            'path_name' => '文件路径',
            'size' => '文件大小',
            'file_type' => '文件类型',
            'extension' => '文件扩展名',
            'mime' => '文件MIME类型',
            'md5' => '文件MD5校验',
            'sha1' => '文件SHA-1校验',
            'user_id' => '文件创建者用户ID',
            'admin_id' => '后台操作管理员ID',
        ]);
        if(is_null($key)){
            return $attributes;
        }else{
            return $attributes[$key] ?? parent::attributes($key);
        }
    }

}

