<?php

namespace Z1px\App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Z1px\App\Http\Services\Admins\AdminsService;
use Z1px\App\Http\Services\Users\UsersService;

class FilesModel extends Model
{

    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 's_files';

    /**
     * 允许添加的字段名
     *
     * @var array
     */
    protected $fillable = ['original_name', 'disk', 'visibility', 'path_name', 'size', 'file_type', 'extension', 'mime', 'md5', 'sha1', 'user_type', 'user_id', 'admin_id'];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['file_type_name', 'visibility_name', 'size_format', 'user_type_name', 'base64', 'image', 'user', 'admin'];

    /**
     * 文件类型列表
     * @var array
     */
    public $list_file_path = [
        1 => 'image', // 图片文件类型
        2 => 'audio', // 音频文件类型
        3 => 'video', // 视频文件类型
        4 => 'text', // 文本文件类型
        5 => 'application', // 应用文件类型
        6 => 'archive', // 归档文件类型
    ];

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
        return $this->list_file_type[$this->file_type] ?? '未知';
    }

    public function getVisibilityNameAttribute()
    {
        return $this->list_visibility[$this->visibility] ?? '未知';
    }

    public function getSizeFormatAttribute()
    {
        $value = $this->size;
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
        return $this->list_user_type[$this->user_type] ?? '未知';
    }

    public function getBase64Attribute()
    {
        if(Storage::disk($this->disk)->exists($this->path_name)
            && 1 === ($this->file_type) && !empty($this->extension)){
            $value = "data:image/{$this->extension};base64," . chunk_split(base64_encode(Storage::disk($this->disk)->get($this->path_name))); // 合成图片的base64编码;
        }else{
            $value = '';
        }
        return $value;
    }

    public function getImageAttribute()
    {
        switch ($this->file_type){
            case 1:
                $value = $this->file_to_image($this->id);
                break;
            default:
                $value = '';
        }
        return $value;
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

    public function getAdminAttribute()
    {
        if($this->admin_id > 0){
            $value = app(AdminsService::class)->toInfo($this->admin_id);
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

