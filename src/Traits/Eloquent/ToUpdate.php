<?php

namespace Z1px\App\Traits\Eloquent;


/**
 * 模型操作，数据更新
 * Trait ToUpdate
 * @package App\Traits\Eloquent
 */
trait ToUpdate
{

    /**
     * 更新数据
     *
     * @return array
     */
    public function toUpdate(...$args)
    {
        $params = request()->input();

        // 更新前执行修改参数
        if(method_exists(static::class, 'toUpdateParams')){
            $params = $this->toUpdateParams($params, ...$args);
        }

        // 参数合法性验证
        validator($params, $this->rules('update'), $this->messages(), $this->attributes())->validate();

        $data = $this->find($params['id']);
        if(empty($data)){
            return [
                'code' => 0,
                'message' => '修改失败，数据不存在！'
            ];
        }
        $data->setBeforeAttributes($data->getAttributes());

        // 赋值
        $data->fill($params);
        unset($params);

        // 更新前执行
        if(method_exists(static::class, 'toUpdating')){
            $updating = $data->toUpdating(...$args);
            if('object' !== gettype($updating)){
                if('array' === gettype($updating)){
                    return $updating;
                }else{
                    return [
                        'code' => 0,
                        'message' => is_string($updating) ? $updating : '更新失败，更新行为被阻住！'
                    ];
                }
            }
            unset($updating);
        }
        unset($args);

        if($data->save()){
            if(!$data->wasChanged()){
                return [
                    'code' => 0,
                    'message' => '没有数据被修改',
                ];
            }

            // 更新后执行
            if(method_exists(static::class, 'toUpdated')){
                $data->toUpdated();
            }

            return [
                'code' => 1,
                'message' => '修改成功',
                'data' => $data->getAttributes()
            ];
        }else{
            return [
                'code' => 0,
                'message' => '修改失败'
            ];
        }
    }

}
