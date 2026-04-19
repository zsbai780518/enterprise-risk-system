<template>
  <view class="container">
    <!-- 头部欢迎区 -->
    <view class="header">
      <view class="welcome">
        <text class="title">欢迎使用</text>
        <text class="subtitle">企业风险分析系统</text>
      </view>
      <view class="search-box" @click="goSearch">
        <text class="search-icon">🔍</text>
        <text class="search-placeholder">搜索企业名称/统一社会信用代码</text>
      </view>
    </view>

    <!-- 快捷功能区 -->
    <view class="quick-actions">
      <view class="action-item" @click="goCollect">
        <view class="action-icon">📊</view>
        <text class="action-text">企业采集</text>
      </view>
      <view class="action-item" @click="goAnalysis">
        <view class="action-icon">📈</view>
        <text class="action-text">风险分析</text>
      </view>
      <view class="action-item" @click="goReport">
        <view class="action-icon">📋</view>
        <text class="action-text">生成报告</text>
      </view>
      <view class="action-item" @click="goMonitor">
        <view class="action-icon">🔔</view>
        <text class="action-text">风险预警</text>
      </view>
    </view>

    <!-- 我的企业列表 -->
    <view class="section">
      <view class="section-header">
        <text class="section-title">我的企业</text>
        <text class="section-more" @click="goCompanyList">查看全部 ></text>
      </view>
      
      <view v-if="companies.length > 0" class="company-list">
        <view 
          v-for="company in companies" 
          :key="company.id" 
          class="company-item"
          @click="goCompanyDetail(company.id)"
        >
          <view class="company-info">
            <text class="company-name">{{ company.name }}</text>
            <text class="company-code">{{ company.unified_social_code }}</text>
          </view>
          <view class="risk-tag" :class="'risk-' + company.risk_level">
            {{ getRiskLevelText(company.risk_level) }}
          </view>
        </view>
      </view>
      
      <view v-else class="empty-state">
        <text class="empty-text">暂无绑定企业</text>
        <text class="empty-hint">点击搜索添加企业</text>
      </view>
    </view>

    <!-- 风险动态 -->
    <view class="section">
      <view class="section-header">
        <text class="section-title">风险动态</text>
      </view>
      <view class="risk-news">
        <view class="news-item">
          <text class="news-dot">•</text>
          <text class="news-text">新增企业风险预警功能，实时监控企业风险变化</text>
        </view>
        <view class="news-item">
          <text class="news-dot">•</text>
          <text class="news-text">系统 V1.0 版本上线，支持基础风险分析功能</text>
        </view>
      </view>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      companies: []
    };
  },
  
  onLoad() {
    this.loadCompanies();
  },
  
  onPullDownRefresh() {
    this.loadCompanies();
    uni.stopPullDownRefresh();
  },
  
  methods: {
    // 加载企业列表
    async loadCompanies() {
      try {
        const res = await api.company.list();
        if (res.code === 200) {
          this.companies = res.data || [];
        }
      } catch (e) {
        console.log('加载企业列表失败', e);
      }
    },
    
    // 获取风险等级文本
    getRiskLevelText(level) {
      const map = { 1: '低风险', 2: '中风险', 3: '高风险' };
      return map[level] || '未知';
    },
    
    // 跳转搜索
    goSearch() {
      uni.navigateTo({ url: '/pages/company/search' });
    },
    
    // 跳转企业采集
    goCollect() {
      uni.navigateTo({ url: '/pages/company/search?type=collect' });
    },
    
    // 跳转风险分析
    goAnalysis() {
      if (this.companies.length === 0) {
        uni.showToast({ title: '请先添加企业', icon: 'none' });
        return;
      }
      uni.navigateTo({ url: '/pages/risk/analysis?company_id=' + this.companies[0].id });
    },
    
    // 跳转报告
    goReport() {
      uni.switchTab({ url: '/pages/report/list' });
    },
    
    // 跳转监控
    goMonitor() {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 跳转企业列表
    goCompanyList() {
      uni.switchTab({ url: '/pages/company/list' });
    },
    
    // 跳转企业详情
    goCompanyDetail(id) {
      uni.navigateTo({ url: '/pages/company/detail?id=' + id });
    }
  }
};
</script>

<style scoped>
.container {
  padding: 20rpx;
  background: #f5f5f5;
  min-height: 100vh;
}

.header {
  background: linear-gradient(135deg, #1890ff 0%, #096dd9 100%);
  border-radius: 16rpx;
  padding: 40rpx 30rpx;
  margin-bottom: 30rpx;
}

.welcome {
  margin-bottom: 30rpx;
}

.title {
  font-size: 32rpx;
  color: #ffffff;
  font-weight: bold;
  display: block;
}

.subtitle {
  font-size: 24rpx;
  color: rgba(255,255,255,0.8);
  display: block;
  margin-top: 10rpx;
}

.search-box {
  background: rgba(255,255,255,0.2);
  border-radius: 50rpx;
  padding: 20rpx 30rpx;
  display: flex;
  align-items: center;
}

.search-icon {
  font-size: 28rpx;
  margin-right: 16rpx;
}

.search-placeholder {
  font-size: 26rpx;
  color: rgba(255,255,255,0.7);
}

.quick-actions {
  display: flex;
  justify-content: space-between;
  background: #ffffff;
  border-radius: 16rpx;
  padding: 30rpx 20rpx;
  margin-bottom: 30rpx;
}

.action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.action-icon {
  font-size: 48rpx;
  margin-bottom: 12rpx;
}

.action-text {
  font-size: 24rpx;
  color: #333;
}

.section {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20rpx;
}

.section-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
}

.section-more {
  font-size: 24rpx;
  color: #999;
}

.company-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.company-info {
  flex: 1;
}

.company-name {
  font-size: 28rpx;
  color: #333;
  font-weight: 500;
  display: block;
}

.company-code {
  font-size: 22rpx;
  color: #999;
  display: block;
  margin-top: 8rpx;
}

.risk-tag {
  padding: 8rpx 16rpx;
  border-radius: 20rpx;
  font-size: 22rpx;
}

.risk-1 {
  background: #f0f9ff;
  color: #1890ff;
}

.risk-2 {
  background: #fff7e6;
  color: #fa8c16;
}

.risk-3 {
  background: #fff1f0;
  color: #f5222d;
}

.empty-state {
  text-align: center;
  padding: 60rpx 0;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
  display: block;
}

.empty-hint {
  font-size: 24rpx;
  color: #ccc;
  display: block;
  margin-top: 12rpx;
}

.risk-news {
  padding: 10rpx 0;
}

.news-item {
  display: flex;
  padding: 16rpx 0;
}

.news-dot {
  color: #1890ff;
  margin-right: 12rpx;
}

.news-text {
  font-size: 26rpx;
  color: #666;
  flex: 1;
}
</style>
