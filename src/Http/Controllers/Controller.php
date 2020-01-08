<?php

namespace Z1px\App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
        return json($result, $status, $headers);
    }

    /**
     * 跳转
     * @param array $result
     */
    protected function jump(array $result = [], int $status = 200, array $headers = [], $view='jump')
    {
        return jump($view, $result, $status, $headers);
    }

}
