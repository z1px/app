<?php

namespace Z1px\App\Traits\Eloquent;


/**
 * 模型操作，数据列表
 * Trait ToList
 * @package App\Traits\Eloquent
 */
trait ToList
{

    /**
     * 列表分页
     *
     * @return array
     */
    public function toList(...$args)
    {
        $params = request()->input();

        // 查询前执行修改参数
        if(method_exists(static::class, 'toListParams')){
            $params = $this->toListParams($params, ...$args);
        }

        // 参数合法性验证
        validator($params, $this->rules('list'), $this->messages(), $this->attributes())->validate();

        if(method_exists(static::class, 'trashed')){
            $data = $this->withTrashed();
        }else{
            $data = $this;
        }

        // 查询前执行
        if(method_exists(static::class, 'toListing')){
            $data = $this->toListing($data, ...$args);
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

        // 查询条件
        $data = $this->toWhere($data, $params);

        $data = $data->orderBy('id', 'desc')
            ->paginate($params['limit'] ?? 10);

        if(empty($data->items()) && isset($params['page']) && $params['page'] > 1){
            throw new \Exception("数据不存在");
        }
        unset($params);

        // 查询后执行
        if(method_exists(static::class, 'toListed')){
            $data = $this->toListed($data);
        }

        return $data;
    }

}
