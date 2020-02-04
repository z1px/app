<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 2020/1/9
 * Time: 4:45 下午
 */


namespace Z1px\App\Http\Logics;


use Illuminate\Support\Facades\Hash;
use Z1px\App\Http\Services\Admins\AdminsLoginService;
use Z1px\App\Http\Services\Admins\AdminsService;
use Z1px\App\Models\Admins\AdminsModel;
use Z1px\Tool\Verify;

class AdminsLogic
{

    private $admins_model = AdminsModel::class;

    /**
     * 登录
     * @return array
     */
    public function login()
    {
        $params = request()->input();

        // 参数合法性验证
        validator($params, app($this->admins_model)->rules('login'), app($this->admins_model)->messages(), app($this->admins_model)->attributes())->validate();

        $data = app($this->admins_model)->select(['id', 'username', 'nickname', 'mobile', 'email', 'file_id', 'status', 'login_failure', 'password', 'access_token']);

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
        $data->setBeforeAttributes($data->getAttributes());

        request()->login = $data;

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

        app(AdminsLoginService::class)->toAdd([
            'admin_id' => $data->id,
            'username' => $data->username,
            'nickname' => $data->nickname,
            'mobile' => $data->mobile,
            'email' => $data->email,
        ]);

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
        $data = request()->login;

        if(empty($data)){
            return [
                'code' => -1,
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
     * 修改登录信息
     * @return array
     */
    public function update()
    {
        request()->offsetSet('id', request()->login->id);
        if(request()->offsetExists('username')){
            request()->offsetUnset('username');
        }
        if(request()->input('password') && !request()->input('old_password')){
            return [
                'code' => 0,
                'message' => '请输入旧密码'
            ];
        }

        $result = app(AdminsService::class)->toUpdate();

        if(1 === $result['code']){
            if(request()->input('password')){
                $result = [
                    'code' => -1,
                    'message' => '修改成功，请重新登录'
                ];
            }
        }

        return $result;
    }

    /**
     * 退出登录
     * @return array
     */
    public function logout()
    {
        $data = request()->login;

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

    /**
     * 权限
     * @return array
     */
    public function permissions()
    {
        $data = request()->permissions;

        return [
            'code' => 1,
            'message' => 'data normal',
            'data' => $data
        ];
    }

}
