<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/26
 * Time: 12:16 上午
 */


namespace Z1px\App\Http\Controllers\Admin\Admins;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\Admins\AdminsService;

class AdminsController extends Controller
{

    public function __construct()
    {
        $this->model = AdminsService::class;
    }

    /**
     * 账号列表
     */
    public function index()
    {
        return $this->_index();
    }

    /**
     * 账号信息
     */
    public function info()
    {
        return $this->_info();
    }

    /**
     * 添加账号
     */
    public function add()
    {
        return $this->_add();
    }

    /**
     * 修改账号
     */
    public function update()
    {
        return $this->_update();
    }

    /**
     * 删除账号
     */
    public function delete()
    {
        return $this->_delete();
    }

    /**
     * 恢复账号
     */
    public function restore()
    {
        return $this->_restore();
    }

}
