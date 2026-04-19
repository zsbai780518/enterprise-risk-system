<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 图表控制器（可视化数据）
// +----------------------------------------------------------------------

namespace app\controller;

use app\model\Company;
use think\facade\Request;

class Chart
{
    /**
     * 股权结构图数据
     * @return \think\response\Json
     */
    public function equity()
    {
        $companyId = Request::param('company_id', 0);
        if (!$companyId) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        $company = Company::find($companyId);
        if (!$company) {
            return json(['code' => 404, 'msg' => '企业不存在']);
        }
        
        // 获取股东信息
        $shareholders = db('ers_shareholder')
            ->where('company_id', $companyId)
            ->order('capital_ratio', 'desc')
            ->select()
            ->toArray();
        
        // 构建 ECharts 树图数据
        $treeData = [
            'name' => $company->name,
            'value' => $company->registered_capital ?? 0,
            'children' => []
        ];
        
        foreach ($shareholders as $shareholder) {
            $treeData['children'][] = [
                'name' => $shareholder['shareholder_name'],
                'value' => $shareholder['capital_contribution'] ?? 0,
                'symbolSize' => $shareholder['capital_ratio'] ?? 0,
                'type' => $shareholder['shareholder_type'] == 1 ? '自然人' : '企业',
                'ratio' => $shareholder['capital_ratio'] . '%'
            ];
        }
        
        return json([
            'code' => 200,
            'msg' => 'success',
            'data' => [
                'company' => [
                    'name' => $company->name,
                    'uscc' => $company->unified_social_code,
                    'capital' => $company->registered_capital
                ],
                'tree' => $treeData,
                'list' => $shareholders
            ]
        ]);
    }
    
    /**
     * 风险地图数据（雷达图）
     * @return \think\response\Json
     */
    public function risk()
    {
        $companyId = Request::param('company_id', 0);
        if (!$companyId) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        // 获取各维度风险数量
        $riskTypes = [
            1 => ['name' => '工商风险', 'value' => 0],
            2 => ['name' => '法律诉讼', 'value' => 0],
            3 => ['name' => '资质合规', 'value' => 0],
            4 => ['name' => '知识产权', 'value' => 0],
            5 => ['name' => '舆情风险', 'value' => 0],
            6 => ['name' => '经营风险', 'value' => 0]
        ];
        
        $stats = db('ers_risk')
            ->where('company_id', $companyId)
            ->field('risk_type, count(*) as count')
            ->group('risk_type')
            ->select()
            ->toArray();
        
        foreach ($stats as $stat) {
            if (isset($riskTypes[$stat['risk_type']])) {
                $riskTypes[$stat['risk_type']]['value'] = $stat['count'];
            }
        }
        
        // 获取风险等级分布（饼图数据）
        $levelData = db('ers_risk')
            ->where('company_id', $companyId)
            ->field('risk_level, count(*) as count')
            ->group('risk_level')
            ->select()
            ->toArray();
        
        $levelMap = [1 => '低风险', 2 => '中风险', 3 => '高风险'];
        $pieData = [];
        foreach ($levelData as $item) {
            $pieData[] = [
                'name' => $levelMap[$item['risk_level']] ?? '未知',
                'value' => $item['count']
            ];
        }
        
        return json([
            'code' => 200,
            'msg' => 'success',
            'data' => [
                'radar' => array_values($riskTypes),
                'pie' => $pieData
            ]
        ]);
    }
    
    /**
     * 资质合规图谱数据
     * @return \think\response\Json
     */
    public function qualification()
    {
        $companyId = Request::param('company_id', 0);
        if (!$companyId) {
            return json(['code' => 400, 'msg' => '企业 ID 不能为空']);
        }
        
        $qualifications = db('ers_qualification')
            ->where('company_id', $companyId)
            ->select()
            ->toArray();
        
        $statusMap = [0 => '过期', 1 => '有效', 2 => '缺失'];
        $statusColors = ['#f5222d', '#52c41a', '#faad14'];
        
        $data = [];
        foreach ($qualifications as $qual) {
            $data[] = [
                'name' => $qual['qual_name'],
                'status' => $statusMap[$qual['status']] ?? '未知',
                'status_code' => $qual['status'],
                'color' => $statusColors[$qual['status']] ?? '#999',
                'valid_to' => $qual['valid_to'],
                'is_required' => $qual['is_required']
            ];
        }
        
        // 统计
        $statistics = [
            'total' => count($qualifications),
            'valid' => count(array_filter($qualifications, fn($q) => $q['status'] == 1)),
            'expired' => count(array_filter($qualifications, fn($q) => $q['status'] == 0)),
            'missing' => count(array_filter($qualifications, fn($q) => $q['status'] == 2))
        ];
        
        return json([
            'code' => 200,
            'msg' => 'success',
            'data' => [
                'list' => $data,
                'statistics' => $statistics
            ]
        ]);
    }
}
