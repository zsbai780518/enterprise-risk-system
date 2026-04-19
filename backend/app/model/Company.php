<?php
// +----------------------------------------------------------------------
// | 企业风险分析系统 - 企业模型
// +----------------------------------------------------------------------

namespace app\model;

use think\Model;

class Company extends Model
{
    // 定义数据表（含前缀）
    protected $table = 'ers_company';
    
    // 定义主键
    protected $pk = 'id';
    
    // 定义时间字段名称
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
    // 类型转换
    protected $type = [
        'id' => 'integer',
        'registered_capital' => 'float',
        'risk_score' => 'float',
        'risk_level' => 'integer',
        'establishment_date' => 'timestamp',
    ];
    
    /**
     * 搜索企业
     * @param string $keyword 关键词（企业名称/统一社会信用代码）
     * @return array
     */
    public static function search(string $keyword): array
    {
        $where = function ($query) use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%")
                  ->whereOr('unified_social_code', 'like', "%{$keyword}%");
        };
        
        $list = self::where($where)
            ->field('id, name, unified_social_code, legal_representative, registered_capital, establishment_date, business_status, risk_level, risk_score')
            ->limit(20)
            ->select()
            ->toArray();
        
        return $list;
    }
    
    /**
     * 根据统一社会信用代码获取企业详情
     * @param string $uscc 统一社会信用代码
     * @return Company|null
     */
    public static function getByUscc(string $uscc)
    {
        return self::where('unified_social_code', $uscc)->find();
    }
    
    /**
     * 更新企业风险评分
     * @param int $companyId 企业 ID
     * @return array ['risk_score'=>评分，'risk_level'=>等级]
     */
    public static function updateRiskScore(int $companyId): array
    {
        // 获取该企业的所有风险记录
        $risks = db('ers_risk')
            ->where('company_id', $companyId)
            ->select()
            ->toArray();
        
        if (empty($risks)) {
            $riskScore = 0;
            $riskLevel = 1;
        } else {
            // 计算风险评分（简单加权算法）
            $weights = [1 => 5, 2 => 15, 3 => 30]; // 低中高权重
            $totalWeight = 0;
            
            foreach ($risks as $risk) {
                $totalWeight += $weights[$risk['risk_level']] ?? 0;
            }
            
            // 归一化到 0-100
            $riskScore = min(100, $totalWeight);
            
            // 确定风险等级
            if ($riskScore >= 70) {
                $riskLevel = 3; // 高
            } elseif ($riskScore >= 30) {
                $riskLevel = 2; // 中
            } else {
                $riskLevel = 1; // 低
            }
        }
        
        // 更新企业表
        self::update([
            'id' => $companyId,
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'data_updated_at' => date('Y-m-d H:i:s')
        ]);
        
        return ['risk_score' => $riskScore, 'risk_level' => $riskLevel];
    }
    
    /**
     * 获取企业详情（含关联数据）
     * @param int $id 企业 ID
     * @return array
     */
    public static function getDetail(int $id): array
    {
        $company = self::find($id);
        if (!$company) {
            return [];
        }
        
        $data = $company->toArray();
        
        // 获取股东信息
        $data['shareholders'] = db('ers_shareholder')
            ->where('company_id', $id)
            ->order('capital_ratio', 'desc')
            ->select()
            ->toArray();
        
        // 获取风险信息
        $data['risks'] = db('ers_risk')
            ->where('company_id', $id)
            ->order('risk_level', 'desc')
            ->limit(50)
            ->select()
            ->toArray();
        
        // 获取资质信息
        $data['qualifications'] = db('ers_qualification')
            ->where('company_id', $id)
            ->select()
            ->toArray();
        
        // 获取知识产权信息
        $data['intellectual_properties'] = db('ers_intellectual_property')
            ->where('company_id', $id)
            ->select()
            ->toArray();
        
        // 风险统计
        $data['risk_statistics'] = db('ers_risk')
            ->where('company_id', $id)
            ->field('risk_type, risk_level, count(*) as count')
            ->group('risk_type, risk_level')
            ->select()
            ->toArray();
        
        return $data;
    }
}
