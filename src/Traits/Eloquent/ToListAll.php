<?php

namespace Z1px\App\Traits\Eloquent;


/**
 * 模型操作，数据列表
 * Trait ToListAll
 * @package App\Traits\Eloquent
 */
trait ToListAll
{

    /**
     * 列表所有
     *
     * @return array
     */
    public function toListAll(...$args)
    {
        $params = request()->input();

        // 查询前执行修改参数
        if(method_exists(static::class, 'toListAllParams')){
            $params = $this->toListAllParams($params, ...$args);
        }

        // 参数合法性验证
        $validator = validator($params, $this->rules('list'), $this->messages(), $this->attributes());
        if ($validator->fails()) {
            return [
                'code' => 0,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ];
        }
        unset($validator);

        if(method_exists(static::class, 'trashed')){
            $data = $this->withTrashed();
        }else{
            $data = $this;
        }

        // 查询前执行
        if(method_exists(static::class, 'toListAlling')){
            $data = $this->toListAlling($data, ...$args);
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
        unset($params);

        $data = $data->orderBy('id', 'asc')->get();

        // 查询后执行
        if(method_exists(static::class, 'toListAlled')){
            $data = $this->toListAlled($data);
        }

        return $data;
    }

}
