<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/9/29
 * Time: 6:17 下午
 */


namespace Z1px\App\Http\Services\Users;


use Z1px\App\Models\Users\UsersModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class UsersService extends UsersModel
{

    use ToAdd, ToUpdate, ToInfo, ToDelete, ToList, ToRestore;

    /**
     * 获取信息前修改数据
     * @param array $params
     * @param null $id
     * @return array
     */
    protected function toInfoParams(array $params, $id=null)
    {
        if(!is_null($id)){
            $params['id'] = $id;
        }
        return $params;
    }

    /**
     * 查询条件构造
     * @param $data
     * @param array $params
     * @return mixed
     */
    protected function toWhere(object $data, array $params): object
    {
        if(!empty($params)){
            foreach ($params as $key=>$value){
                if(empty($value) && !is_numeric($value)) continue;
                switch ($key){
                    case 'keyword':
                        $data = $data->where(function ($query) use ($value) {
                            $query->where('nickname', 'like', "%{$value}%")
                                ->orWhere('username', 'like', "%{$value}%")
                                ->orWhere('mobile', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%");
                        });
                        break;
                    case 'start_date':
                        $data = $data->whereDate('created_at', '>=', $value);
                        break;
                    case 'end_date':
                        $data = $data->whereDate('created_at', '<=', $value);
                        break;
                    case 'date_range':
                        list($start_date, $end_date) = $value;
                        $data = $data->whereDate('created_at', '>=', $start_date)
                            ->whereDate('created_at', '<=', $end_date);
                        unset($start_date, $end_date);
                        break;
                    default:
                        if($this->isFillable($key)){
                            if(is_array($value)){
                                $data = $data->whereIn($key, $value);
                            }else{
                                $data = $data->where($key, $value);
                            }
                        }
                }
            }
        }
        return $data;
    }

}
