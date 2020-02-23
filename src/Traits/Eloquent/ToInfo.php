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
        $validator = validator($params, $this->rules('info'), $this->messages(), $this->attributes());
        if ($validator->fails()) {
            return [
                'code' => 0,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ];
        }
        unset($validator);

        $data = $this;

        // 查询前执行
        if(method_exists(static::class, 'toInfoing')){
            $data = $this->toInfoing($data, ...$args);
            if('object' !== gettype($data)){
                if('array' === gettype($data)){
                    return $data;
                }else{
                    return [
                        'code' => 0,
                        'message' => is_string($data) ? $data : '查询失败，查询行为被阻住！'
                    ];
                }
            }
        }
        unset($args);

        $data = $data->find($params['id']);
        unset($params);
        if(empty($data)){
            throw new \Exception("数据不存在");
        }

        // 查询后执行
        if(method_exists(static::class, 'toInfoed')){
            $data = $this->toInfoed($data);
        }

        return $data;
    }

}
