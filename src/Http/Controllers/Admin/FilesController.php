<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/1
 * Time: 3:13 下午
 */


namespace Z1px\App\Http\Controllers\Admin;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\FilesService;

class FilesController extends Controller
{

    protected function __construct()
    {
        $this->model = FilesService::class;
    }

    /**
     * 文件资源列表
     */
    public function index()
    {
        return $this->_index();
    }

    /**
     * 设置文件可见
     */
    public function visible()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toVisible());
        }
        return $this->error();
    }

    /**
     * 设置文件不可见
     */
    public function invisible()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toInvisible());
        }
        return $this->error();
    }

    /**
     * 删除文件
     */
    public function delete()
    {
        return $this->_delete();
    }
}
