<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/11/1
 * Time: 3:13 下午
 */


namespace Z1px\App\Http\Controllers\Admin;


use Z1px\App\Http\Controllers\AdminController;

class FilesController extends AdminController
{

    /**
     * 文件资源列表
     */
    public function index()
    {
        return view('admin.files.index')
            ->with('search_form', $this->buildSearchForm([
                [
                    'name' => 'table',
                    'title' => app('files_service')->attributes('table'),
                    'type' => 'text',
                ],
                [
                    'name' => 'file_type',
                    'title' => app('files_service')->attributes('file_type'),
                    'type' => 'select',
                    'list' => app('files_service')->list_file_type,
                ],
                [
                    'name' => 'visibility',
                    'title' => app('files_service')->attributes('visibility'),
                    'type' => 'select',
                    'list' => app('files_service')->list_visibility,
                ],
                [
                    'name' => 'start_time',
                    'title' => app('files_service')->attributes('start_time'),
                    'type' => 'text',
                ],
                [
                    'name' => 'end_time',
                    'title' => app('files_service')->attributes('end_time'),
                    'type' => 'text',
                ]
            ]))
            ->with('data', app('files_service')->toList())
            ->with('list_menu', app('menu_logic')->toList());
    }

    /**
     * 设置文件可见
     */
    public function visible()
    {
        if(request()->ajax()) {
            return $this->json(app('files_service')->toVisible());
        }
        return $this->error();
    }

    /**
     * 设置文件不可见
     */
    public function invisible()
    {
        if(request()->ajax()) {
            return $this->json(app('files_service')->toInvisible());
        }
        return $this->error();
    }

    /**
     * 删除文件
     */
    public function delete()
    {
        if(request()->ajax()) {
            return $this->json(app('files_service')->toDelete());
        }
        return $this->error();
    }
}
