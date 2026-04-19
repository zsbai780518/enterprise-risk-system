<template>
  <view class="container">
    <!-- 筛选栏 -->
    <view class="filter-bar">
      <view class="filter-item" @click="toggleTypeFilter">
        <text class="filter-label">类型：</text>
        <text class="filter-value">{{ typeFilterText }}</text>
        <text class="filter-arrow">›</text>
      </view>
      <view class="filter-item" @click="toggleLevelFilter">
        <text class="filter-label">等级：</text>
        <text class="filter-value">{{ levelFilterText }}</text>
        <text class="filter-arrow">›</text>
      </view>
    </view>

    <!-- 风险列表 -->
    <view class="risk-list" v-if="risks.length > 0">
      <view 
        v-for="(item, index) in risks" 
        :key="index"
        class="risk-card"
      >
        <view class="risk-header">
          <view class="risk-type-badge">{{ getRiskTypeText(item.risk_type) }}</view>
          <view class="risk-level-badge" :class="'risk-' + item.risk_level">
            {{ getRiskLevelText(item.risk_level) }}
          </view>
        </view>
        <text class="risk-title">{{ item.risk_title }}</text>
        <text class="risk-content">{{ item.risk_content || '暂无详情' }}</text>
        <view class="risk-footer">
          <text class="risk-source" v-if="item.risk_source">来源：{{ item.risk_source }}</text>
          <text class="risk-date">{{ formatDate(item.created_at) }}</text>
        </view>
        <view class="risk-amount" v-if="item.amount">
          <text class="amount-label">涉及金额：</text>
          <text class="amount-value">¥{{ item.amount }}</text>
        </view>
      </view>
    </view>

    <!-- 空状态 -->
    <view class="empty-state" v-if="!loading && risks.length === 0">
      <text class="empty-icon">📋</text>
      <text class="empty-text">暂无风险记录</text>
    </view>

    <!-- 加载状态 -->
    <view class="loading-state" v-if="loading">
      <text>加载中...</text>
    </view>

    <!-- 加载更多 -->
    <view class="load-more" v-if="risks.length > 0 && hasMore">
      <text>加载更多...</text>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      companyId: 0,
      risks: [],
      loading: false,
      hasMore: false,
      typeFilter: 0,
      levelFilter: 0
    };
  },
  
  computed: {
    typeFilterText() {
      const map = { 0: '全部', 1: '工商', 2: '法律诉讼', 3: '资质合规', 4: '知识产权', 5: '舆情', 6: '经营' };
      return map[this.typeFilter] || '全部';
    },
    levelFilterText() {
      const map = { 0: '全部', 1: '低', 2: '中', 3: '高' };
      return map[this.levelFilter] || '全部';
    }
  },
  
  onLoad(options) {
    if (options.company_id) {
      this.companyId = options.company_id;
      this.loadRisks();
    }
  },
  
  methods: {
    // 加载风险列表
    async loadRisks() {
      this.loading = true;
      try {
        const res = await api.risk.list(this.companyId, {
          risk_type: this.typeFilter,
          risk_level: this.levelFilter
        });
        if (res.code === 200) {
          this.risks = res.data || [];
        }
      } catch (e) {
        console.error('加载失败', e);
      } finally {
        this.loading = false;
      }
    },
    
    // 切换类型筛选
    toggleTypeFilter() {
      const types = [0, 1, 2, 3, 4, 5, 6];
      const currentIndex = types.indexOf(this.typeFilter);
      const nextIndex = (currentIndex + 1) % types.length;
      this.typeFilter = types[nextIndex];
      this.loadRisks();
    },
    
    // 切换等级筛选
    toggleLevelFilter() {
      const levels = [0, 1, 2, 3];
      const currentIndex = levels.indexOf(this.levelFilter);
      const nextIndex = (currentIndex + 1) % levels.length;
      this.levelFilter = levels[nextIndex];
      this.loadRisks();
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
    
    // 格式化日期
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      const y = date.getFullYear();
      const m = String(date.getMonth() + 1).padStart(2, '0');
      const d = String(date.getDate()).padStart(2, '0');
      return `${y}-${m}-${d}`;
    }
  }
};
</script>

<style scoped>
.container {
  min-height: 100vh;
  background: #f5f5f5;
}

.filter-bar {
  background: #ffffff;
  padding: 20rpx 30rpx;
  display: flex;
  border-bottom: 1rpx solid #f0f0f0;
}

.filter-item {
  display: flex;
  align-items: center;
  padding: 16rpx 24rpx;
  background: #f5f5f5;
  border-radius: 12rpx;
  margin-right: 20rpx;
}

.filter-label {
  font-size: 24rpx;
  color: #999;
}

.filter-value {
  font-size: 26rpx;
  color: #333;
  font-weight: 500;
}

.filter-arrow {
  font-size: 28rpx;
  color: #999;
  margin-left: 8rpx;
}

.risk-list {
  padding: 30rpx;
}

.risk-card {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
}

.risk-header {
  display: flex;
  align-items: center;
  margin-bottom: 16rpx;
}

.risk-type-badge {
  font-size: 22rpx;
  padding: 6rpx 16rpx;
  border-radius: 8rpx;
  background: #f5f5f5;
  color: #666;
  margin-right: 16rpx;
}

.risk-level-badge {
  font-size: 22rpx;
  padding: 6rpx 16rpx;
  border-radius: 12rpx;
}

.risk-1 { background: #e6f7ff; color: #1890ff; }
.risk-2 { background: #fff7e6; color: #fa8c16; }
.risk-3 { background: #fff1f0; color: #f5222d; }

.risk-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
  display: block;
  margin-bottom: 12rpx;
}

.risk-content {
  font-size: 26rpx;
  color: #666;
  display: block;
  margin-bottom: 16rpx;
  line-height: 1.6;
}

.risk-footer {
  display: flex;
  justify-content: space-between;
  padding-top: 16rpx;
  border-top: 1rpx solid #f0f0f0;
}

.risk-source, .risk-date {
  font-size: 22rpx;
  color: #999;
}

.risk-amount {
  margin-top: 16rpx;
  padding: 16rpx;
  background: #fff7e6;
  border-radius: 12rpx;
  display: flex;
  align-items: center;
}

.amount-label {
  font-size: 24rpx;
  color: #999;
}

.amount-value {
  font-size: 26rpx;
  color: #fa8c16;
  font-weight: bold;
}

.empty-state, .loading-state {
  text-align: center;
  padding: 120rpx 40rpx;
}

.empty-icon {
  font-size: 100rpx;
  display: block;
  margin-bottom: 30rpx;
}

.empty-text {
  font-size: 28rpx;
  color: #999;
}

.load-more {
  text-align: center;
  padding: 30rpx;
  color: #999;
  font-size: 26rpx;
}
</style>
