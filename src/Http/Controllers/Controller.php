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
    protected function _list()
    {
        $list = app($this->model)->toList();
        return $this->json([
            'data' => $list->items(),
            'total' => $list->total()
        ]);
    }

    /**
     * 所有
     */
    protected function _all()
    {
        return $this->json(['data' => app($this->model)->toListAll()]);
    }

    /**
     * 详细
     */
    protected function _info()
    {
        return $this->json(['data' => app($this->model)->toInfo()]);
    }

    /**
     * 添加
     */
    protected function _add()
    {
        return $this->json(app($this->model)->toAdd());
    }

    /**
     * 修改
     */
    protected function _update()
    {
        return $this->json(app($this->model)->toUpdate());
    }

    /**
     * 删除
     */
    protected function _delete()
    {
        return $this->json(app($this->model)->toDelete());
    }

    /**
     * 恢复
     */
    protected function _restore()
    {
        return $this->json(app($this->model)->toRestore());
    }

}
