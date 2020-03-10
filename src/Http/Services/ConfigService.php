<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/10/31
 * Time: 1:39 下午
 */


namespace Z1px\App\Http\Services;


use Z1px\App\Models\ConfigModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToListAll;
use Z1px\App\Traits\Eloquent\ToUpdate;

class ConfigService extends ConfigModel
{

    use ToInfo, ToAdd, ToUpdate, ToDelete, ToList, ToListAll;

    public function toConfig()
    {
        $params = request()->input();

        // 参数合法性验证
        $validator = validator($params, $this->rules('config'), $this->messages(), $this->attributes());
        if ($validator->fails()) {
            return [
                'code' => 0,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ];
        }
        unset($validator);

        // 查询条件
        $data = $this->toWhere($this, $params);
        unset($params)

        $data = $data->first();
        unset($params);
        if(empty($data)){
            throw new \Exception("数据不存在");
        }

        return $data;
    }

}
