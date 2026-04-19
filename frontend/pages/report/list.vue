<template>
  <view class="container">
    <!-- 报告列表 -->
    <view class="report-list" v-if="reports.length > 0">
      <view 
        v-for="(item, index) in reports" 
        :key="index"
        class="report-card"
        @click="goDetail(item.id)"
      >
        <view class="report-header">
          <text class="report-title">{{ item.report_title || '企业风险分析报告' }}</text>
          <view class="report-type-badge">{{ getReportTypeText(item.report_type) }}</view>
        </view>
        <view class="report-info">
          <text class="report-company">{{ item.company_name || '企业名称' }}</text>
          <text class="report-date">{{ formatDate(item.created_at) }}</text>
        </view>
        <view class="report-stats">
          <view class="stat-item">
            <text class="stat-value">{{ item.risk_score || 0 }}</text>
            <text class="stat-label">风险评分</text>
          </view>
          <view class="stat-item">
            <text class="stat-value">{{ item.risk_count || 0 }}</text>
            <text class="stat-label">风险项</text>
          </view>
          <view class="stat-item">
            <view class="level-badge" :class="'level-' + item.risk_level">
              {{ getRiskLevelText(item.risk_level) }}
            </view>
            <text class="stat-label">风险等级</text>
          </view>
        </view>
        <view class="report-actions">
          <view class="action-btn" @click.stop="download(item.id)">
            <text class="action-icon">📥</text>
            <text class="action-text">下载</text>
          </view>
          <view class="action-btn" @click.stop="share(item.id)">
            <text class="action-icon">📤</text>
            <text class="action-text">分享</text>
          </view>
        </view>
      </view>
    </view>

    <!-- 空状态 -->
    <view class="empty-state" v-if="!loading && reports.length === 0">
      <text class="empty-icon">📋</text>
      <text class="empty-text">暂无分析报告</text>
      <text class="empty-hint">先对企业进行风险分析后生成报告</text>
      <button class="analyze-btn" @click="goAnalyze">去分析</button>
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
      reports: [],
      loading: false
    };
  },
  
  onLoad() {
    this.loadReports();
  },
  
  onShow() {
    this.loadReports();
  },
  
  onPullDownRefresh() {
    this.loadReports();
    uni.stopPullDownRefresh();
  },
  
  methods: {
    // 加载报告列表
    async loadReports() {
      this.loading = true;
      try {
        const res = await api.report.list();
        if (res.code === 200) {
          this.reports = res.data || [];
        }
      } catch (e) {
        console.log('加载报告列表失败', e);
      } finally {
        this.loading = false;
      }
    },
    
    // 获取报告类型文本
    getReportTypeText(type) {
      const map = { 1: '简易版', 2: '完整版', 3: '定制版' };
      return map[type] || '未知';
    },
    
    // 获取风险等级文本
    getRiskLevelText(level) {
      const map = { 1: '低', 2: '中', 3: '高' };
      return map[level] || '未知';
    },
    
    // 格式化日期
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      const y = date.getFullYear();
      const m = String(date.getMonth() + 1).padStart(2, '0');
      const d = String(date.getDate()).padStart(2, '0');
      return `${y}-${m}-${d}`;
    },
    
    // 跳转详情
    goDetail(id) {
      uni.navigateTo({ url: '/pages/report/detail?id=' + id });
    },
    
    // 下载报告
    download(id) {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 分享报告
    share(id) {
      uni.showActionSheet({
        itemList: ['分享给微信好友', '分享到朋友圈', '生成图片'],
        success: (res) => {
          uni.showToast({ title: '功能开发中', icon: 'none' });
        }
      });
    },
    
    // 去分析
    goAnalyze() {
      uni.switchTab({ url: '/pages/company/list' });
    }
  }
};
</script>

<style scoped>
.container {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 30rpx;
}

.report-list {
  margin-bottom: 30rpx;
}

.report-card {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
}

.report-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16rpx;
}

.report-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
  flex: 1;
}

.report-type-badge {
  font-size: 22rpx;
  padding: 6rpx 16rpx;
  border-radius: 20rpx;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
}

.report-info {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20rpx;
}

.report-company {
  font-size: 26rpx;
  color: #666;
}

.report-date {
  font-size: 24rpx;
  color: #999;
}

.report-stats {
  display: flex;
  justify-content: space-around;
  padding: 24rpx 0;
  border-top: 1rpx solid #f0f0f0;
  border-bottom: 1rpx solid #f0f0f0;
  margin-bottom: 20rpx;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.stat-value {
  font-size: 32rpx;
  font-weight: bold;
  color: #667eea;
  margin-bottom: 8rpx;
}

.stat-label {
  font-size: 22rpx;
  color: #999;
}

.level-badge {
  font-size: 24rpx;
  padding: 6rpx 16rpx;
  border-radius: 12rpx;
  margin-bottom: 8rpx;
}

.level-1 { background: #e6f7ff; color: #1890ff; }
.level-2 { background: #fff7e6; color: #fa8c16; }
.level-3 { background: #fff1f0; color: #f5222d; }

.report-actions {
  display: flex;
  justify-content: flex-end;
}

.action-btn {
  display: flex;
  align-items: center;
  padding: 12rpx 24rpx;
  background: #f5f5f5;
  border-radius: 12rpx;
  margin-left: 16rpx;
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

.analyze-btn {
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
