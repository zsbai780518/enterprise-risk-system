<template>
  <view class="container">
    <!-- 搜索栏 -->
    <view class="search-bar">
      <view class="search-input" @click="goSearch">
        <text class="search-icon">🔍</text>
        <text class="search-placeholder">搜索企业名称</text>
      </view>
      <view class="add-btn" @click="goCollect">➕</view>
    </view>

    <!-- 企业列表 -->
    <view class="company-list" v-if="companies.length > 0">
      <view 
        v-for="company in companies" 
        :key="company.id" 
        class="company-card"
        @click="goDetail(company.id)"
      >
        <view class="company-main">
          <text class="company-name">{{ company.name }}</text>
          <text class="company-code">{{ company.unified_social_code }}</text>
          <view class="company-tags">
            <text class="tag">{{ company.industry || '未设置行业' }}</text>
            <text class="tag">{{ company.business_status || '存续' }}</text>
          </view>
        </view>
        <view class="company-risk">
          <view class="risk-badge" :class="'risk-' + company.risk_level">
            {{ getRiskLevelText(company.risk_level) }}
          </view>
          <text class="risk-score">评分：{{ company.risk_score || 0 }}</text>
        </view>
        <view class="company-actions">
          <view class="action-btn" @click.stop="analyze(company.id)">
            <text class="action-icon">📊</text>
            <text class="action-text">分析</text>
          </view>
          <view class="action-btn" @click.stop="unbind(company.id)">
            <text class="action-icon">🗑️</text>
            <text class="action-text">解绑</text>
          </view>
        </view>
      </view>
    </view>

    <!-- 空状态 -->
    <view class="empty-state" v-if="!loading && companies.length === 0">
      <text class="empty-icon">🏢</text>
      <text class="empty-text">暂无绑定企业</text>
      <text class="empty-hint">点击右上角添加或搜索企业</text>
      <button class="add-company-btn" @click="goCollect">添加企业</button>
    </view>

    <!-- 加载状态 -->
    <view class="loading-state" v-if="loading">
      <text>加载中...</text>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      companies: [],
      loading: false
    };
  },
  
  onLoad() {
    this.loadCompanies();
  },
  
  onShow() {
    this.loadCompanies();
  },
  
  onPullDownRefresh() {
    this.loadCompanies();
    uni.stopPullDownRefresh();
  },
  
  methods: {
    // 加载企业列表
    async loadCompanies() {
      this.loading = true;
      try {
        const res = await api.company.list();
        if (res.code === 200) {
          this.companies = res.data || [];
        }
      } catch (e) {
        console.log('加载企业列表失败', e);
      } finally {
        this.loading = false;
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
    
    // 跳转采集
    goCollect() {
      uni.navigateTo({ url: '/pages/company/search?type=collect' });
    },
    
    // 跳转详情
    goDetail(id) {
      uni.navigateTo({ url: '/pages/company/detail?id=' + id });
    },
    
    // 风险分析
    analyze(companyId) {
      uni.navigateTo({ url: '/pages/risk/analysis?company_id=' + companyId });
    },
    
    // 解绑企业
    unbind(companyId) {
      uni.showModal({
        title: '确认解绑',
        content: '确定要解绑该企业吗？解绑后仍可重新添加。',
        success: async (res) => {
          if (res.confirm) {
            try {
              const result = await api.company.unbind(companyId);
              if (result.code === 200) {
                uni.showToast({ title: '解绑成功', icon: 'success' });
                this.loadCompanies();
              } else {
                uni.showToast({ title: result.msg, icon: 'none' });
              }
            } catch (e) {
              uni.showToast({ title: '解绑失败', icon: 'none' });
            }
          }
        }
      });
    }
  }
};
</script>

<style scoped>
.container {
  min-height: 100vh;
  background: #f5f5f5;
}

.search-bar {
  background: #ffffff;
  padding: 20rpx 30rpx;
  display: flex;
  align-items: center;
}

.search-input {
  flex: 1;
  background: #f5f5f5;
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
  color: #999;
}

.add-btn {
  font-size: 40rpx;
  margin-left: 20rpx;
  padding: 10rpx 20rpx;
}

.company-list {
  padding: 30rpx;
}

.company-card {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
}

.company-main {
  margin-bottom: 20rpx;
}

.company-name {
  font-size: 32rpx;
  font-weight: bold;
  color: #333;
  display: block;
}

.company-code {
  font-size: 24rpx;
  color: #999;
  display: block;
  margin-top: 10rpx;
}

.company-tags {
  display: flex;
  margin-top: 16rpx;
}

.tag {
  font-size: 22rpx;
  color: #666;
  background: #f5f5f5;
  padding: 6rpx 16rpx;
  border-radius: 20rpx;
  margin-right: 16rpx;
}

.company-risk {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20rpx;
  padding-bottom: 20rpx;
  border-bottom: 1rpx solid #f0f0f0;
}

.risk-badge {
  font-size: 24rpx;
  padding: 8rpx 20rpx;
  border-radius: 20rpx;
}

.risk-1 {
  background: #e6f7ff;
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

.risk-score {
  font-size: 24rpx;
  color: #999;
}

.company-actions {
  display: flex;
}

.action-btn {
  display: flex;
  align-items: center;
  padding: 16rpx 24rpx;
  background: #f5f5f5;
  border-radius: 12rpx;
  margin-right: 20rpx;
}

.action-icon {
  font-size: 28rpx;
  margin-right: 8rpx;
}

.action-text {
  font-size: 24rpx;
  color: #666;
}

.empty-state {
  text-align: center;
  padding: 120rpx 40rpx;
}

.empty-icon {
  font-size: 100rpx;
  display: block;
  margin-bottom: 30rpx;
}

.empty-text {
  font-size: 32rpx;
  color: #666;
  display: block;
  margin-bottom: 16rpx;
}

.empty-hint {
  font-size: 26rpx;
  color: #999;
  display: block;
  margin-bottom: 40rpx;
}

.add-company-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  border: none;
  border-radius: 12rpx;
  height: 88rpx;
  font-size: 28rpx;
  width: 300rpx;
}

.loading-state {
  text-align: center;
  padding: 60rpx;
  color: #999;
}
</style>
