<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/19
 * Time: 3:02 下午
 */


namespace Z1px\App\Http\Controllers\Admin\Admins;


use Z1px\App\Http\Controllers\Controller;
use Z1px\App\Http\Services\Admins\PermissionsService;

class PermissionsController extends Controller
{

    public function __construct()
    {
        $this->model = PermissionsService::class;
    }

    /**
     * 权限列表
     */
    public function index()
    {
        return $this->_index();
    }

    /**
     * 权限详情
     */
    public function info()
    {
        return $this->_info();
    }

    /**
     * 权限信息
     */
    public function getRouteActionByRouteName()
    {
        if(request()->ajax()) {
            return $this->json(['data' => app($this->model)->getRouteActionByRouteName(request()->input('route_name'))]);
        }
        return $this->error();
    }

    /**
     * 添加权限
     */
    public function add()
    {
        return $this->_add();
    }

    /**
     * 修改权限
     */
    public function update()
    {
        return $this->_update();
    }

    /**
     * 删除权限
     */
    public function delete()
    {
        return $this->_delete();
    }

    /**
     * 拖拽移动权限
     */
    public function drop()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toDrop());
        }
        return $this->error();
    }
}
