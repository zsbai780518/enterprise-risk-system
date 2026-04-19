<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 企业控制器
// +----------------------------------------------------------------------

namespace app\controller;

use app\model\Company;
use app\model\User;
use think\facade\Request;
use think\facade\Session;

class Company
{
    /**
     * 企业列表（用户绑定的企业）
     * @return \think\response\Json
     */
    public function index()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }
        
        $userModel = new User();
        $companies = $userModel->getBoundCompanies($userId);
        
        return json(['code' => 200, 'msg' => 'success', 'data' => $companies]);
    }
    
    /**
     * 企业详情
     * @return \think\response\Json
     */
    public function detail()
    {
        $id = Request::param('id', 0);
        if (!$id) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        $detail = Company::getDetail($id);
        if (empty($detail)) {
            return json(['code' => 404, 'msg' => '企业不存在']);
        }
        
        return json(['code' => 200, 'msg' => 'success', 'data' => $detail]);
    }
    
    /**
     * 搜索企业
     * @return \think\response\Json
     */
    public function search()
    {
        $keyword = Request::param('keyword', '');
        if (empty($keyword)) {
            return json(['code' => 400, 'msg' => '搜索关键词不能为空']);
        }
        
        $list = Company::search($keyword);
        
        return json(['code' => 200, 'msg' => 'success', 'data' => $list]);
    }
    
    /**
     * 采集企业数据
     * @return \think\response\Json
     */
    public function collect()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }
        
        $uscc = Request::param('unified_social_code', '');
        if (empty($uscc)) {
            return json(['code' => 400, 'msg' => '统一社会信用代码不能为空']);
        }
        
        // TODO: 实现数据采集逻辑
        // 1. 调用天眼查等 API 获取数据
        // 2. 解析并存储到数据库
        // 3. 记录采集日志
        
        // 临时返回模拟数据
        return json([
            'code' => 200,
            'msg' => '数据采集成功（演示模式）',
            'data' => [
                'company_id' => 1,
                'name' => '演示企业',
                'uscc' => $uscc,
                'collection_status' => 'success'
            ]
        ]);
    }
    
    /**
     * 绑定企业
     * @return \think\response\Json
     */
    public function bind()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }
        
        $companyId = Request::param('company_id', 0);
        if (!$companyId) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        // 检查企业是否存在
        $company = Company::find($companyId);
        if (!$company) {
            return json(['code' => 404, 'msg' => '企业不存在']);
        }
        
        // 检查是否已绑定
        $exist = db('ers_user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->find();
        
        if ($exist) {
            return json(['code' => 400, 'msg' => '该企业已绑定']);
        }
        
        // 创建绑定关系
        $result = db('ers_user_company')->insert([
            'user_id' => $userId,
            'company_id' => $companyId,
            'is_owner' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return json($result ? ['code' => 200, 'msg' => '绑定成功'] : ['code' => 500, 'msg' => '绑定失败']);
    }
    
    /**
     * 解绑企业
     * @return \think\response\Json
     */
    public function unbind()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }
        
        $companyId = Request::param('company_id', 0);
        if (!$companyId) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        $result = db('ers_user_company')
            ->where('user_id', $userId)
            ->where('company_id', $companyId)
            ->delete();
        
        return json($result ? ['code' => 200, 'msg' => '解绑成功'] : ['code' => 500, 'msg' => '解绑失败']);
    }
}
