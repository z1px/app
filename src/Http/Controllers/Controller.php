<?php

namespace Z1px\App\Http\Controllers;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $model;

    /**
     * 404
     * @return \Illuminate\Http\Response
     */
    protected function error()
    {
        return error();
    }

    /**
     * 返回json格式数据
     * @param array $result
     */
    protected function json(array $result = [], int $status = 200, array $headers = [])
    {
        isset($result['code']) || $result['code'] = 1;
        isset($result['message']) || $result['message'] = 'data normal!';

        return json($result, $status, $headers);
    }

    /**
     * 跳转
     * @param array $result
     */
    protected function jump(array $result = [], int $status = 200, array $headers = [], $view='jump')
    {
        isset($result['code']) || $result['code'] = 1;
        isset($result['message']) || $result['message'] = 'data normal!';

        return jump($view, $result, $status, $headers);
    }

    /**
     * 列表
     */
    protected function _index()
    {
        if(request()->ajax()){
            return $this->json(app($this->model)->toList());
        }
        return $this->error();
    }

    /**
     * 所有
     */
    protected function _all()
    {
        if(request()->ajax()){
            return $this->json(['data' => app($this->model)->toListAll()]);
        }
        return $this->error();
    }

    /**
     * 详细
     */
    protected function _info()
    {
        if(request()->ajax()){
            return $this->json(['data' => app($this->model)->toInfo()]);
        }
        return $this->error();
    }

    /**
     * 添加
     */
    protected function _add()
    {
        if(request()->ajax()){
            return $this->json(app($this->model)->toAdd());
        }
        return $this->error();
    }

    /**
     * 修改
     */
    protected function _update()
    {
        if(request()->ajax()){
            return $this->json(app($this->model)->toUpdate());
        }
        return $this->error();
    }

    /**
     * 删除
     */
    protected function _delete()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toDelete());
        }
        return $this->error();
    }

    /**
     * 恢复
     */
    protected function _restore()
    {
        if(request()->ajax()) {
            return $this->json(app($this->model)->toRestore());
        }
        return $this->error();
    }

}
