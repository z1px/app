<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2020/1/9
 * Time: 4:45 下午
 */


namespace Z1px\App\Http\Logics;


use Illuminate\Support\Facades\Hash;
use Z1px\App\Models\Admins\AdminsModel;
use Z1px\Tool\Verify;

class AdminsLogic
{

    private $model = AdminsModel::class;

    /**
     * 登录
     * @return array
     */
    public function login()
    {
        $params = request()->input();

        // 参数合法性验证
        validator($params, $this->model->rules('login'), $this->model->messages(), $this->model->attributes())->validate();

        $data = $this->model->select(['id', 'username', 'nickname', 'mobile', 'email', 'file_id', 'status', 'login_failure', 'password', 'access_token']);

        if(Verify::mobile($params['username'])){
            $data = $data->where('mobile', $params['username']);
        }elseif(Verify::email($params['username'])){
            $data = $data->where('email', $params['username']);
        }else{
            $data = $data->where('username', $params['username']);
        }
        $data = $data->first();

        if(empty($data)){
            return [
                'code' => 0,
                'message' => '账号不存在'
            ];
        }

        if($data->login_failure >=5){
            return [
                'code' => 0,
                'message' => "密码已连续输错{$data->login_failure}次，账号已锁定",
            ];
        }

        if(!Hash::check($params['password'], $data->password)){
            $data->increment('login_failure');
            return [
                'code' => 0,
                'message' => '账号或密码错误',
            ];
        }

        $data->login_failure = 0;
        $data->login_at = date('Y-m-d H:i:s');
        $data->access_token = md5(uniqid($data->id));
        $data->save();

        if(1 !== $data->status){
            return [
                'code' => 0,
                'message' => '该账号已被禁用',
            ];
        }

        return [
            'code' => 1,
            'message' => '登录成功',
            'data' => [
                'id' => $data->id,
                'username' => $data->username,
                'nickname' => $data->nickname,
                'mobile' => $data->mobile,
                'email' => $data->email,
                'avatar' => $data->avatar,
                'status' => $data->status,
                'login_at' => $data->login_at,
                'access_token' => $data->access_token,
            ],
        ];
    }

    /**
     * 获取登录信息
     * @return array
     */
    public function info()
    {
        $params = request()->input();

        // 参数合法性验证
        validator($params, $this->model->rules('loginInfo'), $this->model->messages(), $this->model->attributes())->validate();

        $data = $this->model->select(['id', 'username', 'nickname', 'mobile', 'email', 'file_id', 'status', 'login_at', 'access_token'])
            ->where('access_token', $params['access_token'])
            ->first();

        if(empty($data)){
            return [
                'code' => 0,
                'message' => '登录已过期或未登录'
            ];
        }

        return [
            'code' => 1,
            'message' => '获取成功',
            'data' => [
                'id' => $data->id,
                'username' => $data->username,
                'nickname' => $data->nickname,
                'email' => $data->email,
                'mobile' => $data->mobile,
                'avatar' => $data->avatar,
                'status' => $data->status,
                'login_at' => $data->login_at,
                'access_token' => $data->access_token,
            ],
        ];
    }

    /**
     * 退出登录
     * @return array
     */
    public function logout()
    {
        $params = request()->input();

        // 参数合法性验证
        validator($params, $this->model->rules('logout'), $this->model->messages(), $this->model->attributes())->validate();

        $data = $this->model->select(['id', 'username', 'nickname', 'mobile', 'email', 'file_id', 'status', 'login_at', 'access_token'])
            ->where('access_token', $params['access_token'])
            ->first();

        if(empty($data)){
            return [
                'code' => 1,
                'message' => '已退出'
            ];
        }

        $data->access_token = null;
        if($data->save()){
            return [
                'code' => 1,
                'message' => '退出成功',
            ];
        }else{
            return [
                'code' => 0,
                'message' => '退出失败',
            ];
        }
    }

}
