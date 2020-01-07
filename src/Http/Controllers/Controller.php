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
        return response()->view('404', [], 404);
    }

    /**
     * 返回json格式数据
     * @param array $result
     */
    protected function json(array $result = [], int $status = 200, array $headers = [])
    {
        $result = array_merge([
            'code' => 1,
            'message' => 'data normal',
            'data' => [],
            'timestamp' => time(),
            'runtime' => microtime(true) - request()->server('REQUEST_TIME_FLOAT'),
        ], $result);

        return response()->json($result, $status, $headers);
    }

    /**
     * 跳转
     * @param array $result
     */
    protected function jump(array $result = [], int $status = 200, array $headers = [], $view='jump')
    {
        $result = array_merge([
            'code' => 1,
            'message' => 'data normal',
            'data' => [],
            'url' => 'javascript:history.back().reload();',
            'wait' => 3,
            'timestamp' => time(),
            'runtime' => microtime(true) - request()->server('REQUEST_TIME_FLOAT'),
        ], $result);

        return response()->view($view, $result, $status, $headers);
    }

}
