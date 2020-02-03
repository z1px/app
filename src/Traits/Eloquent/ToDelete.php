<?php

namespace Z1px\App\Traits\Eloquent;


/**
 * 模型操作，数据删除
 * Trait ToDelete
 * @package App\Traits\Eloquent
 */
trait ToDelete
{

    /**
     * 删除数据
     *
     * @return array
     */
    public function toDelete(...$args)
    {
        $params = request()->input();

        // 删除前执行修改参数
        if(method_exists(static::class, 'toDeleteParams')){
            $params = $this->toDeleteParams($params, ...$args);
        }

        // 参数合法性验证
        validator($params, $this->rules('delete'), $this->messages(), $this->attributes())->validate();

        $data = $this->find($params['id']);
        unset($params);
        if(empty($data)){
            return [
                'code' => 0,
                'message' => '删除失败，数据不存在！'
            ];
        }
        $data->setBeforeAttributes($data->getAttributes());

        // 删除前执行
        if(method_exists(static::class, 'toDeleting')){
            $data = $this->toDeleting($data, ...$args);
            if('object' !== gettype($data)){
                if('array' === gettype($data)){
                    return $data;
                }else{
                    return [
                        'code' => 0,
                        'message' => is_string($data) ? $data : '删除失败，删除行为被阻住！'
                    ];
                }
            }
        }
        unset($args);

        if($data->delete()){

            // 删除后执行
            if(method_exists(static::class, 'toDeleted')){
                $data = $this->toDeleted($data);
            }

            return [
                'code' => 1,
                'message' => '删除成功',
                'data' => $data
            ];
        }else{
            return [
                'code' => 0,
                'message' => '删除失败'
            ];
        }
    }

}
