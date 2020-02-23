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
        $validator = validator($params, $this->rules('restore'), $this->messages(), $this->attributes());
        if ($validator->fails()) {
            return [
                'code' => 0,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ];
        }

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
            $data = $this->toRestoring($data, ...$args);
            if('object' !== gettype($data)){
                if('array' === gettype($data)){
                    return $data;
                }else{
                    return [
                        'code' => 0,
                        'message' => is_string($data) ? $data : '恢复失败，恢复行为被阻住！'
                    ];
                }
            }
        }
        unset($args);

        if ($data->trashed()){

            if($data->restore()){

                // 恢复后执行
                if(method_exists(static::class, 'toRestored')){
                    $data = $this->toRestored($data);
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
