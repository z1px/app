<?php

namespace Z1px\App\Traits\Eloquent;


/**
 * 模型操作，数据恢复软删除
 * Trait ToRestore
 * @package App\Traits\Eloquent
 */
trait ToRestore
{

    /**
     * 恢复软删除
     *
     * @return array
     */
    public function toRestore(...$args)
    {
        $params = request()->input();

        // 恢复前执行修改参数
        if(method_exists(static::class, 'toRestoreParams')){
            $params = $this->toRestoreParams($params, ...$args);
        }

        // 参数合法性验证
        validator($params, $this->rules('restore'), $this->messages(), $this->attributes())->validate();

        $data = $this->withTrashed()->find($params['id']);
        unset($params);
        if(empty($data)){
            return [
                'code' => 0,
                'message' => '恢复失败，数据不存在！'
            ];
        }
        $data->setBeforeAttributes($data->getAttributes());

        // 恢复前执行
        if(method_exists(static::class, 'toRestoring')){
            $restoring = $data->toRestoring(...$args);
            if('object' !== gettype($restoring)){
                if('array' === gettype($restoring)){
                    return $restoring;
                }else{
                    return [
                        'code' => 0,
                        'message' => is_string($restoring) ? $restoring : '恢复失败，恢复行为被阻住！'
                    ];
                }
            }
            unset($restoring);
        }
        unset($args);

        if ($data->trashed()){

            if($data->restore()){

                // 恢复后执行
                if(method_exists(static::class, 'toRestored')){
                    $data->toRestored();
                }

                return [
                    'code' => 1,
                    'message' => '恢复成功',
                    'data' => $data
                ];
            }else{
                return [
                    'code' => 0,
                    'message' => '恢复失败'
                ];
            }
        }else{
            return [
                'code' => 0,
                'message' => '恢复失败，数据已存在！'
            ];
        }
    }

}
