<template>
  <view class="container">
    <!-- 加载状态 -->
    <view class="loading-state" v-if="loading">
      <text>加载中...</text>
    </view>

    <view v-else-if="company">
      <!-- 企业基本信息 -->
      <view class="company-header">
        <text class="company-name">{{ company.name }}</text>
        <text class="company-code">{{ company.unified_social_code }}</text>
        <view class="risk-info">
          <view class="risk-badge" :class="'risk-' + company.risk_level">
            {{ getRiskLevelText(company.risk_level) }}
          </view>
          <text class="risk-score">风险评分：{{ company.risk_score || 0 }}</text>
        </view>
      </view>

      <!-- 快捷操作 -->
      <view class="actions-bar">
        <view class="action-item" @click="goAnalysis">
          <text class="action-icon">📊</text>
          <text class="action-text">风险分析</text>
        </view>
        <view class="action-item" @click="goEquity">
          <text class="action-icon">🌳</text>
          <text class="action-text">股权结构</text>
        </view>
        <view class="action-item" @click="goReport">
          <text class="action-icon">📋</text>
          <text class="action-text">生成报告</text>
        </view>
        <view class="action-item" @click="copyCode">
          <text class="action-icon">📋</text>
          <text class="action-text">复制代码</text>
        </view>
      </view>

      <!-- 基本信息 -->
      <view class="section">
        <text class="section-title">基本信息</text>
        <view class="info-grid">
          <view class="info-item">
            <text class="info-label">法定代表人</text>
            <text class="info-value">{{ company.legal_representative || '-' }}</text>
          </view>
          <view class="info-item">
            <text class="info-label">注册资本</text>
            <text class="info-value">{{ company.registered_capital || '-' }}万元</text>
          </view>
          <view class="info-item">
            <text class="info-label">成立日期</text>
            <text class="info-value">{{ company.establishment_date || '-' }}</text>
          </view>
          <view class="info-item">
            <text class="info-label">经营状态</text>
            <text class="info-value">{{ company.business_status || '-' }}</text>
          </view>
          <view class="info-item full-width">
            <text class="info-label">经营范围</text>
            <text class="info-value">{{ company.business_scope || '-' }}</text>
          </view>
        </view>
      </view>

      <!-- 股东信息 -->
      <view class="section" v-if="company.shareholders && company.shareholders.length > 0">
        <text class="section-title">股东信息（{{ company.shareholders.length }}）</text>
        <view class="shareholder-list">
          <view 
            v-for="(item, index) in company.shareholders" 
            :key="index"
            class="shareholder-item"
          >
            <view class="shareholder-info">
              <text class="shareholder-name">{{ item.shareholder_name }}</text>
              <text class="shareholder-type">{{ item.shareholder_type == 1 ? '自然人' : '企业' }}</text>
            </view>
            <view class="shareholder-data">
              <text class="shareholder-ratio">{{ item.capital_ratio }}%</text>
              <text class="shareholder-capital">{{ item.capital_contribution }}万元</text>
            </view>
          </view>
        </view>
      </view>

      <!-- 风险信息 -->
      <view class="section" v-if="company.risks && company.risks.length > 0">
        <text class="section-title">风险信息（{{ company.risks.length }}）</text>
        <view class="risk-list">
          <view 
            v-for="(item, index) in company.risks" 
            :key="index"
            class="risk-item"
            @click="goRiskDetail(item.id)"
          >
            <view class="risk-item-left">
              <view class="risk-type-badge">{{ getRiskTypeText(item.risk_type) }}</view>
              <text class="risk-title">{{ item.risk_title }}</text>
            </view>
            <view class="risk-level-badge" :class="'risk-' + item.risk_level">
              {{ getRiskLevelText(item.risk_level) }}
            </view>
          </view>
        </view>
        <view class="view-more" @click="goRiskList">
          <text>查看全部风险 ›</text>
        </view>
      </view>

      <!-- 资质信息 -->
      <view class="section" v-if="company.qualifications && company.qualifications.length > 0">
        <text class="section-title">资质信息（{{ company.qualifications.length }}）</text>
        <view class="qualification-list">
          <view 
            v-for="(item, index) in company.qualifications" 
            :key="index"
            class="qualification-item"
          >
            <text class="qualification-name">{{ item.qual_name }}</text>
            <view class="qualification-status">
              <text class="status-badge" :class="'status-' + item.status">
                {{ getQualStatusText(item.status) }}
              </text>
              <text class="qualification-date" v-if="item.valid_to">有效期至：{{ item.valid_to }}</text>
            </view>
          </view>
        </view>
      </view>

      <!-- 知识产权 -->
      <view class="section" v-if="company.intellectual_properties && company.intellectual_properties.length > 0">
        <text class="section-title">知识产权（{{ company.intellectual_properties.length }}）</text>
        <view class="ip-list">
          <view 
            v-for="(item, index) in company.intellectual_properties" 
            :key="index"
            class="ip-item"
          >
            <view class="ip-header">
              <view class="ip-type-badge">{{ getIpTypeText(item.ip_type) }}</view>
              <text class="ip-name">{{ item.ip_name }}</text>
            </view>
            <text class="ip-status">状态：{{ item.status }}</text>
          </view>
        </view>
      </view>
    </view>

    <!-- 空状态 -->
    <view class="empty-state" v-if="!loading && !company">
      <text class="empty-text">企业信息不存在</text>
      <button class="back-btn" @click="goBack">返回</button>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      companyId: 0,
      company: null,
      loading: true
    };
  },
  
  onLoad(options) {
    if (options.id) {
      this.companyId = options.id;
      this.loadDetail();
    }
  },
  
  methods: {
    // 加载详情
    async loadDetail() {
      this.loading = true;
      try {
        const res = await api.company.detail(this.companyId);
        if (res.code === 200 && res.data) {
          this.company = res.data;
        } else {
          uni.showToast({ title: '企业不存在', icon: 'none' });
        }
      } catch (e) {
        console.error('加载失败', e);
        uni.showToast({ title: '加载失败', icon: 'none' });
      } finally {
        this.loading = false;
      }
    },
    
    // 获取风险等级文本
    getRiskLevelText(level) {
      const map = { 1: '低风险', 2: '中风险', 3: '高风险' };
      return map[level] || '未知';
    },
    
    // 获取风险类型文本
    getRiskTypeText(type) {
      const map = { 
        1: '工商', 2: '法律诉讼', 3: '资质合规', 
        4: '知识产权', 5: '舆情', 6: '经营' 
      };
      return map[type] || '其他';
    },
    
    // 获取资质状态文本
    getQualStatusText(status) {
      const map = { 0: '过期', 1: '有效', 2: '缺失' };
      return map[status] || '未知';
    },
    
    // 获取知识产权类型文本
    getIpTypeText(type) {
      const map = { 1: '商标', 2: '专利', 3: '软著', 4: '版权' };
      return map[type] || '其他';
    },
    
    // 跳转风险分析
    goAnalysis() {
      uni.navigateTo({ url: '/pages/risk/analysis?company_id=' + this.companyId });
    },
    
    // 跳转股权结构
    goEquity() {
      uni.navigateTo({ url: '/pages/risk/chart?type=equity&company_id=' + this.companyId });
    },
    
    // 生成报告
    goReport() {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 复制代码
    copyCode() {
      uni.setClipboardData({
        data: this.company.unified_social_code,
        success: () => {
          uni.showToast({ title: '已复制', icon: 'success' });
        }
      });
    },
    
    // 跳转风险详情
    goRiskDetail(id) {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 跳转风险列表
    goRiskList() {
      uni.navigateTo({ url: '/pages/risk/list?company_id=' + this.companyId });
    },
    
    // 返回
    goBack() {
      uni.navigateBack();
    }
  }
};
</script>

<style scoped>
.container {
  min-height: 100vh;
  background: #f5f5f5;
}

.loading-state, .empty-state {
  text-align: center;
  padding: 120rpx 40rpx;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
  display: block;
  margin-bottom: 30rpx;
}

.back-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  border: none;
  border-radius: 12rpx;
  height: 80rpx;
  font-size: 28rpx;
  width: 200rpx;
}

.company-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 40rpx 30rpx;
  color: #ffffff;
}

.company-name {
  font-size: 36rpx;
  font-weight: bold;
  display: block;
}

.company-code {
  font-size: 24rpx;
  opacity: 0.8;
  display: block;
  margin-top: 10rpx;
}

.risk-info {
  display: flex;
  align-items: center;
  margin-top: 20rpx;
}

.risk-badge {
  font-size: 24rpx;
  padding: 8rpx 20rpx;
  border-radius: 20rpx;
  background: rgba(255,255,255,0.2);
  margin-right: 20rpx;
}

.risk-1 { color: #69c0ff; }
.risk-2 { color: #ffd666; }
.risk-3 { color: #ff7875; }

.risk-score {
  font-size: 24rpx;
  opacity: 0.8;
}

.actions-bar {
  background: #ffffff;
  padding: 30rpx;
  display: flex;
  justify-content: space-around;
  margin-bottom: 30rpx;
}

.action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.action-icon {
  font-size: 40rpx;
  margin-bottom: 10rpx;
}

.action-text {
  font-size: 24rpx;
  color: #666;
}

.section {
  background: #ffffff;
  margin-bottom: 30rpx;
  padding: 30rpx;
}

.section-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
  display: block;
  margin-bottom: 24rpx;
}

.info-grid {
  display: flex;
  flex-wrap: wrap;
}

.info-item {
  width: 50%;
  margin-bottom: 24rpx;
}

.info-item.full-width {
  width: 100%;
}

.info-label {
  font-size: 24rpx;
  color: #999;
  display: block;
  margin-bottom: 8rpx;
}

.info-value {
  font-size: 26rpx;
  color: #333;
  display: block;
}

.shareholder-list, .risk-list, .qualification-list, .ip-list {
  border-top: 1rpx solid #f0f0f0;
}

.shareholder-item, .risk-item, .qualification-item, .ip-item {
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.shareholder-info, .risk-item-left, .ip-header {
  display: flex;
  align-items: center;
  margin-bottom: 12rpx;
}

.shareholder-name, .risk-title, .ip-name {
  font-size: 28rpx;
  color: #333;
  margin-left: 16rpx;
}

.shareholder-type, .risk-type-badge, .ip-type-badge {
  font-size: 22rpx;
  padding: 4rpx 12rpx;
  border-radius: 8rpx;
  background: #f5f5f5;
  color: #666;
}

.shareholder-data {
  display: flex;
  justify-content: flex-end;
}

.shareholder-ratio {
  font-size: 26rpx;
  color: #667eea;
  font-weight: bold;
  margin-right: 20rpx;
}

.shareholder-capital {
  font-size: 24rpx;
  color: #999;
}

.risk-level-badge {
  font-size: 22rpx;
  padding: 6rpx 16rpx;
  border-radius: 20rpx;
}

.risk-item-left {
  flex: 1;
}

.qualification-status {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 12rpx;
}

.status-badge {
  font-size: 22rpx;
  padding: 4rpx 12rpx;
  border-radius: 8rpx;
}

.status-0 { background: #fff1f0; color: #f5222d; }
.status-1 { background: #f6ffed; color: #52c41a; }
.status-2 { background: #fff7e6; color: #fa8c16; }

.qualification-date {
  font-size: 22rpx;
  color: #999;
}

.view-more {
  text-align: center;
  padding: 20rpx 0;
  color: #667eea;
  font-size: 26rpx;
}
</style>
