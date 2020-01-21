<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2019/9/29
 * Time: 6:17 下午
 */


namespace Z1px\App\Http\Services\Admins;


use Illuminate\Support\Facades\Hash;
use Z1px\App\Models\Admins\AdminsModel;
use Z1px\App\Traits\Eloquent\ToAdd;
use Z1px\App\Traits\Eloquent\ToDelete;
use Z1px\App\Traits\Eloquent\ToInfo;
use Z1px\App\Traits\Eloquent\ToList;
use Z1px\App\Traits\Eloquent\ToRestore;
use Z1px\App\Traits\Eloquent\ToUpdate;

class AdminsService extends AdminsModel
{

    use ToAdd, ToUpdate, ToInfo, ToDelete, ToRestore, ToList;

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
                if(empty($value)) continue;
                switch ($key){
                    case 'keyword':
                        $data = $data->where(function ($query) use ($value) {
                            $query->where('nickname', 'like', "%{$value}%")
                                ->orWhere('username', 'like', "%{$value}%")
                                ->orWhere('mobile', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%");
                        });
                        break;
                    case 'start_time':
                        $data = $data->whereDate('created_at', '>=', $value);
                        break;
                    case 'end_time':
                        $data = $data->whereDate('created_at', '<=', $value);
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

    protected function toUpdating()
    {
        if(request()->input('old_password') && !Hash::check(request()->input('old_password'), $this->getOriginal('password'))){
            return [
                'code' => 0,
                'message' => '密码错误'
            ];
        }
        if($this->getAttribute('password') && $this->getOriginal('password') !== $this->getAttribute('password')){
            $this->setAttribute('access_token', null);
        }
        return $this;
    }

}
