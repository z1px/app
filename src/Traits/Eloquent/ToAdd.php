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
        $this->fill($params);
        unset($params);

        // 新增前执行
        if(method_exists(static::class, 'toAdding')){
            $adding = $this->toAdding(...$args);
            if('object' !== gettype($adding)){
                if('array' === gettype($adding)){
                    return $adding;
                }else{
                    return [
                        'code' => 0,
                        'message' => is_string($adding) ? $adding : '新增失败，新增行为被阻住！'
                    ];
                }
            }
            unset($adding);
        }
        unset($args);

        if($this->save()){

            // 新增后执行
            if(method_exists(static::class, 'toAdded')){
                $this->toAdded();
            }

            return [
                'code' => 1,
                'message' => '新增成功',
                'data' => $this
            ];
        }else{
            return [
                'code' => 0,
                'message' => '新增失败'
            ];
        }
    }

}
