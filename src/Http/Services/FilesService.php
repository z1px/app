<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/31
 * Time: 1:39 下午
 */


namespace Z1px\App\Http\Services;


use Z1px\App\Models\FilesModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToUpdate;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Z1px\Tool\IP;

class FilesService extends FilesModel
{

    use ToAdd, ToInfo, ToUpdate, ToDelete, ToList;

    /**
     * 添加前修改参数
     * @param $params
     * @param array $data
     * @return array
     */
    protected function toAddParams(array $params, array $data = [])
    {
        $params = array_merge($params, $data);
        return $params;
    }

    /**
     * 文件上传
     * @return array
     */
    public function upload()
    {
        $files = request()->file();
        if(0 === count($files) || !is_array($files)){
            return [
                'code' => 0,
                'message' => '没有文件上传'
            ];
        }
        $file = reset($files);
        unset($files);

        if(!$file->isValid()){
            return [
                'code' => 0,
                'message' => $file->getError()
            ];
        }

        $filesystems = config('filesystems', []);
        $options = ['disk' => $filesystems['default'] ?? 'local'];
        if(!isset($filesystems['disks'][$options['disk']])){
            return [
                'code' => 0,
                'message' => '文件上传配置错误'
            ];
        }
        $path = $this->list_file_path[$this->getFileType($file->extension())] ?? 'files'; // 存储路径
        $visibility='public'; // 文件可见性，public可见，private不可见

        $data = [
            'original_name' => $file->getClientOriginalName(), // 文件原始名称
            'disk' => $options['disk'] ?? '', // 文件存储磁盘名称
            'visibility' => $visibility, // 文件可见性，public可见，private不可见
            'path_name' => $file->store($path, $options), // 文件路径
            'size' => $file->getSize(), // 文件大小，单位字节b
            'file_type' => $this->getFileType($file->extension()), // 文件类型：1-图片文件类型，2-音频文件类型，3-视频文件类型，4-文本文件类型，5-应用文件类型，6-归档文件类型
            'extension' => strtolower($file->extension()), // 文件扩展名
            'mime' => $file->getMimeType(), // 文件MIME类型
            'md5' => '', // 文件MD5校验
            'sha1' => '', // 文件SHA-1校验
            'user_type' => 1, // 用户类型
            'user_id' => request()->login->id, // 文件创建者用户ID
        ];
        if(Storage::disk($data['disk'])->exists($data['path_name'])){
            if($visibility !== Storage::disk($data['disk'])->getVisibility($data['path_name'])){
                Storage::disk($data['disk'])->setVisibility($data['path_name'], $visibility);
            }
            $root = config("filesystems.disks.{$data['disk']}.root", '') . '/';
            if(is_file($root . $data['path_name'])){
                $data['md5'] = md5_file($root . $data['path_name']);
                $data['sha1'] = sha1_file($root . $data['path_name']);
            }
            unset($root);
        }

        $result = $this->toAdd($data);
        if(1 === $result['code']){
            $result['message'] = '上传成功';
        }else{
            $result['message'] = '上传失败';
        }

        return $result;
    }

    /**
     * base64文件存储
     * @return array
     */
    public function uploadBase64()
    {
        $data = request()->input('file');
        if(empty($data)){
            return [
                'code' => 0,
                'message' => '文件内容不存在'
            ];
        }
        preg_match("/data:([a-z]+\/([a-z0-9-+.]+)(;[a-z-]+=[a-z0-9-]+)?)?(;base64)?,([a-z0-9!$&',()*+;=\-._~:@\/?%\s]*)/i", $data, $match);
        if(empty($match) || !isset($match[5]) || empty($match[5])){
            return [
                'code' => 0,
                'message' => '文件格式错误'
            ];
        }
        $filesystems = config('filesystems', []);
        $options = ['disk' => $filesystems['default'] ?? 'local'];
        if(!isset($filesystems['disks'][$options['disk']])){
            return [
                'code' => 0,
                'message' => '文件上传配置错误'
            ];
        }

        $tmp = tempnam(sys_get_temp_dir(), 'img_');
        file_put_contents($tmp, base64_decode($match[5]));
        $file = new File($tmp);
        $path = $this->list_file_path[$this->getFileType($file->extension())] ?? 'files'; // 存储路径
        $visibility='public'; // 文件可见性，public可见，private不可见

        $data = [
            'original_name' => basename("{$tmp}.{$match[2]}"), // 文件原始名称
            'disk' => $options['disk'] ?? '', // 文件存储磁盘名称
            'visibility' => $visibility, // 文件可见性，public可见，private不可见
            'path_name' => Storage::putFile($path, $file, $options), // 文件路径
            'size' => $file->getSize(), // 文件大小，单位字节b
            'file_type' => $this->getFileType($file->extension()), // 文件类型：1-图片文件类型，2-音频文件类型，3-视频文件类型，4-文本文件类型，5-应用文件类型，6-归档文件类型
            'extension' => strtolower($file->extension()), // 文件扩展名
            'mime' => $file->getMimeType(), // 文件MIME类型
            'md5' => '', // 文件MD5校验
            'sha1' => '', // 文件SHA-1校验
            'user_type' => 1, // 用户类型
            'user_id' => request()->login->id, // 文件创建者用户ID
        ];
        if(Storage::disk($data['disk'])->exists($data['path_name'])){
            if($visibility !== Storage::disk($data['disk'])->getVisibility($data['path_name'])){
                Storage::disk($data['disk'])->setVisibility($data['path_name'], $visibility);
            }
            $root = config("filesystems.disks.{$data['disk']}.root", '') . '/';
            if(is_file($root . $data['path_name'])){
                $data['md5'] = md5_file($root . $data['path_name']);
                $data['sha1'] = sha1_file($root . $data['path_name']);
            }
            unset($root);
        }
        unset($match, $file);
        unlink($tmp);

        $result = $this->toAdd($data);
        if(1 === $result['code']){
            $result['message'] = '上传成功';
        }else{
            $result['message'] = '上传失败';
        }

        return $result;
    }

    /**
     * 删除数据后
     * @return $this
     */
    protected function toDeleted(object $data)
    {
        if(Storage::disk($data->disk)->exists($data->path_name)){
            Storage::disk($data->disk)->delete($data->path_name);
        }
        return $data;
    }

    /**
     * 获取文件信息前修改数据
     * @param array $params
     * @param null $id
     * @return array
     */
    protected function toInfoParams(array $params, $id=null)
    {
        if(isset($params['sn']) && !empty($params['sn'])){
            try{
                $params['id'] = decrypt($params['sn']);
            }catch (\Exception $exception){
                $params['id'] = 0;
            }
        }else{
            $params['id'] = 0;
        }
        if(!is_null($id)){
            $params['id'] = $id;
        }

        return $params;
    }

    /**
     * 获取文件信息前
     * @return FilesService|null
     */
    protected function toInfoing(object $data)
    {
        $data = $data->withTrashed();
        return $data;
    }

    /**
     * 获取文件信息后
     * @return FilesService|null
     */
    protected function toInfoed(object $data)
    {
        if(!Storage::disk($data->disk)->exists($data->path_name) || 'public' !== Storage::disk($data->disk)->getVisibility($data->path_name)){
            $data = null;
        }
        return $data;
    }

    /**
     * 设置文件可见
     * @param $id
     * @return array
     */
    public function toVisible($id=null)
    {
        if(empty($id)){
            $id = request()->input('id', 0);
        }
        $data = $this->find($id);
        if(empty($data)){
            $result = [
                'code' => 0,
                'message' => '文件不存在'
            ];
            return $result;
        }
        $data->setBeforeAttributes($data->getAttributes());

        if(Storage::disk($data->disk)->exists($data->path_name)){
            $data->visibility = Storage::disk($data->disk)->getVisibility($data->path_name);
            if('public' === $data->visibility){
                $result = [
                    'code' => 1,
                    'message' => '文件已设置可见'
                ];
            }else{
                $data->visibility = 'public';
                if(Storage::disk($data->disk)->setVisibility($data->path_name, $data->visibility)){
                    if($data->save()){
                        $result = [
                            'code' => 1,
                            'message' => '文件设置可见成功'
                        ];
                    }else{
                        $result = [
                            'code' => 0,
                            'message' => '文件设置可见失败'
                        ];
                    }
                }else{
                    $result = [
                        'code' => 0,
                        'message' => '文件设置可见失败'
                    ];
                }
            }
        }else{
            $data->delete();
            $result = [
                'code' => 0,
                'message' => '设置可见失败，文件不存在'
            ];
        }
        return $result;
    }

    /**
     * 设置文件不可见
     * @param $id
     * @return array
     */
    public function toInvisible($id=null)
    {
        if(empty($id)){
            $id = request()->input('id', 0);
        }
        $data = $this->find($id);
        if(empty($data)){
            $result = [
                'code' => 1,
                'message' => '文件不存在'
            ];
            return $result;
        }
        $data->setBeforeAttributes($data->getAttributes());
        if(Storage::disk($data->disk)->exists($data->path_name)){
            $data->visibility = Storage::disk($data->disk)->getVisibility($data->path_name);
            if('private' === $data->visibility){
                $result = [
                    'code' => 1,
                    'message' => '文件已设置不可见'
                ];
            }else{
                $data->visibility = 'private';
                if(Storage::disk($data->disk)->setVisibility($data->path_name, $data->visibility)){
                    if($data->save()){
                        $result = [
                            'code' => 1,
                            'message' => '文件设置不可见成功'
                        ];
                    }else{
                        $result = [
                            'code' => 0,
                            'message' => '文件设置不可见失败'
                        ];
                    }
                }else{
                    $result = [
                        'code' => 0,
                        'message' => '文件设置不可见失败'
                    ];
                }
            }
        }else{
            $data->delete();
            $result = [
                'code' => 1,
                'message' => '设置不可见完成，文件不存在'
            ];
        }
        return $result;
    }

//    public $list_mime = [
//        //applications
//        'ai' => 'application/postscript',
//        'eps' => 'application/postscript',
//        'exe' => 'application/octet-stream',
//        'doc' => 'application/vnd.ms-word',
//        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
//        'xls' => 'application/vnd.ms-excel',
//        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
//        'ppt' => 'application/vnd.ms-powerpoint',
//        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
//        'pdf' => 'application/pdf',
//        'xml' => 'application/xml',
//        'odt' => 'application/vnd.oasis.opendocument.text',
//        'swf' => 'application/x-shockwave-flash',
//        // archives
//        'gz' => 'application/x-gzip',
//        'tgz' => 'application/x-gzip',
//        'bz' => 'application/x-bzip2',
//        'bz2' => 'application/x-bzip2',
//        'tbz' => 'application/x-bzip2',
//        'zip' => 'application/zip',
//        'rar' => 'application/x-rar',
//        'tar' => 'application/x-tar',
//        '7z' => 'application/x-7z-compressed',
//        // texts
//        'txt' => 'text/plain',
//        'php' => 'text/x-php',
//        'html' => 'text/html',
//        'htm' => 'text/html',
//        'js' => 'text/javascript',
//        'css' => 'text/css',
//        'rtf' => 'text/rtf',
//        'rtfd' => 'text/rtfd',
//        'py' => 'text/x-python',
//        'java' => 'text/x-java-source',
//        'rb' => 'text/x-ruby',
//        'sh' => 'text/x-shellscript',
//        'pl' => 'text/x-perl',
//        'sql' => 'text/x-sql',
//        // images
//        'bmp' => 'image/x-ms-bmp',
//        'jpg' => 'image/jpeg',
//        'jpeg' => 'image/jpeg',
//        'gif' => 'image/gif',
//        'png' => 'image/png',
//        'tif' => 'image/tiff',
//        'tiff' => 'image/tiff',
//        'tga' => 'image/x-targa',
//        'psd' => 'image/vnd.adobe.photoshop',
//        'svg' => 'image/svg+xml',
//        // audio
//        'mp3' => 'audio/mpeg',
//        'mid' => 'audio/midi',
//        'ogg' => 'audio/ogg',
//        'mp4a' => 'audio/mp4',
//        'wav' => 'audio/wav',
//        'wma' => 'audio/x-ms-wma',
//        // video
//        'avi' => 'video/x-msvideo',
//        'dv' => 'video/x-dv',
//        'mp4' => 'video/mp4',
//        'mpeg' => 'video/mpeg',
//        'mpg' => 'video/mpeg',
//        'mov' => 'video/quicktime',
//        'wm' => 'video/x-ms-wmv',
//        'flv' => 'video/x-flv',
//        'mkv' => 'video/x-matroska'
//    ];

    public function getFileType($extension)
    {
        switch (strtolower($extension)){
            case 'bmp':
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
            case 'tif':
            case 'tiff':
            case 'tga':
            case 'psd':
            case 'svg':
                $type = 1;
                break;
            case 'mp3':
            case 'mid':
            case 'ogg':
            case 'mp4a':
            case 'wav':
            case 'wma':
                $type = 2;
                break;
            case 'avi':
            case 'dv':
            case 'mp4':
            case 'mpeg':
            case 'mpg':
            case 'mov':
            case 'wm':
            case 'flv':
            case 'mkv':
                $type = 3;
                break;
            case 'txt':
            case 'php':
            case 'html':
            case 'htm':
            case 'js':
            case 'css':
            case 'rtf':
            case 'rtfd':
            case 'py':
            case 'java':
            case 'rb':
            case 'sh':
            case 'pl':
            case 'sql':
                $type = 4;
                break;
            case 'gz':
            case 'tgz':
            case 'bz':
            case 'bz2':
            case 'tbz':
            case 'zip':
            case 'rar':
            case 'tar':
            case '7z':
                $type = 5;
                break;
            case 'ai':
            case 'eps':
            case 'exe':
            case 'doc':
            case 'docx':
            case 'xls':
            case 'xlsx':
            case 'ppt':
            case 'pptx':
            case 'pdf':
            case 'xml':
            case 'odt':
            case 'swf':
                $type = 6;
                break;
            default:
                $type = 0;
        }
        return $type;
    }

}
