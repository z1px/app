<?php

namespace Z1px\App\Traits\Eloquent;


/**
 * 模型操作，数据添加
 * Trait ToAdd
 * @package App\Traits\Eloquent
 */
trait ToAdd
{

    /**
     * 新增数据
     *
     * @return array
     */
    public function toAdd(...$args)
    {
        $params = request()->input();

        // 新增前执行修改参数
        if(method_exists(static::class, 'toAddParams')){
            $params = $this->toAddParams($params, ...$args);
        }

        // 参数合法性验证
        validator($params, $this->rules('add'), $this->messages(), $this->attributes())->validate();

        // 赋值
        $data = $this->fill($params);
        unset($params);

        // 新增前执行
        if(method_exists(static::class, 'toAdding')){
            $data = $this->toAdding($data, ...$args);
            if('object' !== gettype($data)){
                if('array' === gettype($data)){
                    return $data;
                }else{
                    return [
                        'code' => 0,
                        'message' => is_string($data) ? $data : '新增失败，新增行为被阻住！'
                    ];
                }
            }
        }
        unset($args);

        if($data->save()){

            // 新增后执行
            if(method_exists(static::class, 'toAdded')){
                $data = $this->toAdded($data);
            }

            return [
                'code' => 1,
                'message' => '新增成功',
                'data' => $data
            ];
        }else{
            return [
                'code' => 0,
                'message' => '新增失败'
            ];
        }
    }

}
