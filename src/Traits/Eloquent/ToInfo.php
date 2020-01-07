<?php

namespace Z1px\App\Traits\Eloquent;


/**
 * 模型操作，获取单条数据
 * Trait ToInfo
 * @package App\Traits\Eloquent
 */
trait ToInfo
{

    /**
     * 单条数据信息
     *
     * @return array
     */
    public function toInfo(...$args)
    {
        $params = request()->input();

        // 查询前执行修改参数
        if(method_exists(static::class, 'toInfoParams')){
            $params = $this->toInfoParams($params, ...$args);
        }

        // 参数合法性验证
        validator($params, $this->rules('info'), $this->messages(), $this->attributes())->validate();

        // 查询前执行
        if(method_exists(static::class, 'toInfoing')){
            $infoing = $this->toInfoing(...$args);
            if('object' !== gettype($infoing)){
                if('array' === gettype($infoing)){
                    return $infoing;
                }else{
                    return [
                        'code' => 0,
                        'message' => is_string($infoing) ? $infoing : '查询失败，查询行为被阻住！'
                    ];
                }
            }
            unset($infoing);
        }
        unset($args);

        $data = $this->find($params['id']);
        unset($params);
        if(empty($data)){
            throw new \Exception("数据不存在");
        }

        // 查询后执行
        if(method_exists(static::class, 'toInfoed')){
            $data = $data->toInfoed();
        }

        return $data;
    }

}
