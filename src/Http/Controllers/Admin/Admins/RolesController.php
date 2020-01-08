<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/24
 * Time: 9:33 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Admins;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\Admins\RolesService;

class RolesController extends Controller
{

    protected function __construct()
    {
        $this->model = RolesService::class;
    }

    /**
     * 角色列表
     */
    public function index()
    {
        return $this->_index();
    }

    /**
     * 添加角色
     */
    public function add()
    {
        return $this->_add();
    }

    /**
     * 修改角色
     */
    public function update()
    {
        return $this->_update();
    }

    /**
     * 删除角色
     */
    public function delete()
    {
        return $this->_delete();
    }

}
