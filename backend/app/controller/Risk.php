<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 风险分析控制器
// +----------------------------------------------------------------------

namespace app\controller;

use app\model\Company;
use think\facade\Request;
use think\facade\Session;

class Risk
{
    /**
     * 风险分析
     * @return \think\response\Json
     */
    public function analysis()
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }
        
        $companyId = Request::param('company_id', 0);
        if (!$companyId) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        // 获取企业详情
        $detail = Company::getDetail($companyId);
        if (empty($detail)) {
            return json(['code' => 404, 'msg' => '企业不存在']);
        }
        
        // 分析风险
        $analysis = [
            'company_id' => $companyId,
            'company_name' => $detail['name'],
            'risk_score' => $detail['risk_score'],
            'risk_level' => $detail['risk_level'],
            'risk_level_text' => $this->getRiskLevelText($detail['risk_level']),
            'total_risks' => count($detail['risks'] ?? []),
            'risk_distribution' => $this->getRiskDistribution($detail['risks'] ?? []),
            'key_risks' => array_slice($detail['risks'] ?? [], 0, 10),
            'qualification_issues' => $this->getQualificationIssues($detail['qualifications'] ?? []),
            'suggestions' => $this->generateSuggestions($detail)
        ];
        
        return json(['code' => 200, 'msg' => 'success', 'data' => $analysis]);
    }
    
    /**
     * 风险列表
     * @return \think\response\Json
     */
    public function list()
    {
        $companyId = Request::param('company_id', 0);
        if (!$companyId) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        $riskType = Request::param('risk_type', 0);
        $riskLevel = Request::param('risk_level', 0);
        
        $where = ['company_id' => $companyId];
        if ($riskType) {
            $where['risk_type'] = $riskType;
        }
        if ($riskLevel) {
            $where['risk_level'] = $riskLevel;
        }
        
        $list = db('ers_risk')
            ->where($where)
            ->order('risk_level', 'desc')
            ->order('created_at', 'desc')
            ->select()
            ->toArray();
        
        return json(['code' => 200, 'msg' => 'success', 'data' => $list]);
    }
    
    /**
     * 风险统计
     * @return \think\response\Json
     */
    public function statistics()
    {
        $companyId = Request::param('company_id', 0);
        if (!$companyId) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        // 按类型统计
        $byType = db('ers_risk')
            ->where('company_id', $companyId)
            ->field('risk_type, count(*) as count')
            ->group('risk_type')
            ->select()
            ->toArray();
        
        // 按等级统计
        $byLevel = db('ers_risk')
            ->where('company_id', $companyId)
            ->field('risk_level, count(*) as count')
            ->group('risk_level')
            ->select()
            ->toArray();
        
        // 趋势统计（近 30 天）
        $trend = db('ers_risk')
            ->where('company_id', $companyId)
            ->where('created_at', '>=', date('Y-m-d', strtotime('-30 days')))
            ->field('DATE(created_at) as date, count(*) as count')
            ->group('DATE(created_at)')
            ->select()
            ->toArray();
        
        return json([
            'code' => 200,
            'msg' => 'success',
            'data' => [
                'by_type' => $byType,
                'by_level' => $byLevel,
                'trend' => $trend
            ]
        ]);
    }
    
    /**
     * 获取风险等级文本
     */
    private function getRiskLevelText(int $level): string
    {
        $texts = [1 => '低', 2 => '中', 3 => '高'];
        return $texts[$level] ?? '未知';
    }
    
    /**
     * 获取风险分布
     */
    private function getRiskDistribution(array $risks): array
    {
        $distribution = [
            'type' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0],
            'level' => [1 => 0, 2 => 0, 3 => 0]
        ];
        
        foreach ($risks as $risk) {
            if (isset($distribution['type'][$risk['risk_type']])) {
                $distribution['type'][$risk['risk_type']]++;
            }
            if (isset($distribution['level'][$risk['risk_level']])) {
                $distribution['level'][$risk['risk_level']]++;
            }
        }
        
        return $distribution;
    }
    
    /**
     * 获取资质问题
     */
    private function getQualificationIssues(array $qualifications): array
    {
        $issues = [];
        foreach ($qualifications as $qual) {
            // 检查过期
            if ($qual['status'] == 0) {
                $issues[] = [
                    'type' => 'expired',
                    'name' => $qual['qual_name'],
                    'valid_to' => $qual['valid_to']
                ];
            }
            // 检查必备资质缺失
            if ($qual['is_required'] == 1 && $qual['status'] == 2) {
                $issues[] = [
                    'type' => 'missing',
                    'name' => $qual['qual_name']
                ];
            }
        }
        return $issues;
    }
    
    /**
     * 生成建议
     */
    private function generateSuggestions(array $detail): array
    {
        $suggestions = [];
        
        // 根据风险等级生成建议
        if ($detail['risk_level'] >= 3) {
            $suggestions[] = '企业风险等级较高，建议立即进行全面风险排查';
        }
        
        // 根据资质问题生成建议
        $qualIssues = $this->getQualificationIssues($detail['qualifications'] ?? []);
        if (!empty($qualIssues)) {
            $suggestions[] = '存在资质问题，请及时办理相关许可证或续期';
        }
        
        // 根据风险信息生成建议
        if (!empty($detail['risks'])) {
            $highRisks = array_filter($detail['risks'], fn($r) => $r['risk_level'] == 3);
            if (count($highRisks) > 0) {
                $suggestions[] = '存在' . count($highRisks) . '项高风险，建议优先处理';
            }
        }
        
        return $suggestions;
    }
}
