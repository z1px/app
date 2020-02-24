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

    protected function toUpdating(object $data)
    {
        if(request()->input('old_password') && !Hash::check(request()->input('old_password'), $data->getOriginal('password'))){
            return [
                'code' => 0,
                'message' => '密码错误'
            ];
        }
        if($data->getAttribute('password') && $data->getOriginal('password') !== $data->getAttribute('password')){
            $data->setAttribute('access_token', null);
        }
        return $data;
    }


    /**
     * 获取角色
     * @return array
     * @throws \Exception
     */
    public function getRoles()
    {
        $data = $this->toInfo();
        return [
            'code' => 1,
            'message' => '角色获取成功',
            'data' =>  $data->roles
        ];
    }

    /**
     * 角色设置
     * @return array
     * @throws \Exception
     */
    public function setRoles()
    {
        $data = $this->toInfo();

        $list_role_ids = request()->input('role_ids');

        try {
            if(empty($list_role_ids)){
                $data->roles()->detach(); // 删除所有中间表ID
            }else{
                $list_pivot_role_ids = $data->roles()->pluck('role_id')->toArray(); // 中间表已存在属性ID
                if(empty($list_pivot_role_ids)){
                    $data->roles()->attach($list_role_ids);
                }else{
                    $detach = array_diff($list_pivot_role_ids, $list_role_ids);
                    if(!empty($detach)){
                        $data->roles()->detach($detach); // 删除中间表中未选择的ID
                    }
                    $attach = array_diff($list_role_ids, $list_pivot_role_ids);
                    if(!empty($attach)){
                        $data->roles()->attach($attach); // 添加中间表中未添加的ID
                    }
                    unset($detach, $attach);
                }
                unset($list_pivot_role_ids);
            }
            unset($list_role_ids);
        }catch (\Exception $exception){
            return [
                'code' => 0,
                'message' => '操作异常'
            ];
        }

        return [
            'code' => 1,
            'message' => '设置成功',
            'data' =>  $data->roles
        ];
    }


}
