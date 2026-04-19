<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 用户控制器
// +----------------------------------------------------------------------

namespace app\controller;

use app\model\User;
use think\facade\Request;
use think\facade\Session;

class User
{
    /**
     * 用户注册
     * @return \think\response\Json
     */
    public function register()
    {
        $data = Request::param();
        
        // 验证必填字段
        if (empty($data['phone']) && empty($data['username'])) {
            return json(['code' => 400, 'msg' => '手机号或用户名不能为空']);
        }
        
        if (empty($data['password'])) {
            return json(['code' => 400, 'msg' => '密码不能为空']);
        }
        
        // 密码长度验证
        if (strlen($data['password']) < 6) {
            return json(['code' => 400, 'msg' => '密码长度不能少于 6 位']);
        }
        
        // 调用模型注册
        $result = User::register($data);
        
        return json($result);
    }
    
    /**
     * 用户登录
     * @return \think\response\Json
     */
    public function login()
    {
        $account = Request::param('account', '');
        $password = Request::param('password', '');
        
        if (empty($account) || empty($password)) {
            return json(['code' => 400, 'msg' => '账号和密码不能为空']);
        }
        
        // 调用模型登录
        $result = User::login($account, $password);
        
        // 登录成功则保存 session
        if ($result['code'] == 200) {
            Session::set('user_id', $result['data']['user_id']);
            Session::set('token', $result['data']['token']);
        }
        
        return json($result);
    }
    
    /**
     * 用户登出
     * @return \think\response\Json
     */
    public function logout()
    {
        Session::clear();
        return json(['code' => 200, 'msg' => '登出成功']);
    }
    
    /**
     * 获取用户信息
     * @return \think\response\Json
     */
    public function info()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }
        
        $user = User::find($userId);
        if (!$user) {
            return json(['code' => 404, 'msg' => '用户不存在']);
        }
        
        $data = $user->toArray();
        unset($data['password']);
        
        // 获取绑定的企业列表
        $userModel = new User();
        $data['companies'] = $userModel->getBoundCompanies($userId);
        
        return json(['code' => 200, 'msg' => 'success', 'data' => $data]);
    }
    
    /**
     * 更新用户资料
     * @return \think\response\Json
     */
    public function profile()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }
        
        $data = Request::param();
        $allowFields = ['username', 'email', 'avatar'];
        
        // 过滤允许更新的字段
        $updateData = [];
        foreach ($allowFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (empty($updateData)) {
            return json(['code' => 400, 'msg' => '没有可更新的字段']);
        }
        
        $result = User::update($updateData, ['id' => $userId]);
        
        return json($result ? ['code' => 200, 'msg' => '更新成功'] : ['code' => 500, 'msg' => '更新失败']);
    }
}
