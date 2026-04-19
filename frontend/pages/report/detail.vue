<template>
  <view class="container">
    <!-- 加载状态 -->
    <view class="loading-state" v-if="loading">
      <text>加载中...</text>
    </view>

    <view v-else-if="report">
      <!-- 报告头部 -->
      <view class="report-header">
        <text class="report-title">{{ report.report_title || '企业风险分析报告' }}</text>
        <view class="report-type-badge">{{ getReportTypeText(report.report_type) }}</view>
      </view>

      <!-- 报告信息 -->
      <view class="report-info-card">
        <view class="info-row">
          <text class="info-label">企业名称</text>
          <text class="info-value">{{ report.company_name || '-' }}</text>
        </view>
        <view class="info-row">
          <text class="info-label">生成时间</text>
          <text class="info-value">{{ formatDate(report.created_at) }}</text>
        </view>
        <view class="info-row">
          <text class="info-label">下载次数</text>
          <text class="info-value">{{ report.download_count || 0 }}</text>
        </view>
      </view>

      <!-- 风险评分 -->
      <view class="score-card">
        <view class="score-circle">
          <text class="score-value">{{ report.risk_score || 0 }}</text>
          <text class="score-label">风险评分</text>
        </view>
        <view class="score-detail">
          <view class="level-badge" :class="'level-' + report.risk_level">
            {{ getRiskLevelText(report.risk_level) }}风险
          </view>
          <text class="risk-count">共 {{ report.risk_count || 0 }} 项风险</text>
        </view>
      </view>

      <!-- 报告内容预览 -->
      <view class="content-section">
        <text class="section-title">报告预览</text>
        <view class="preview-content">
          <text class="preview-text">
            本报告基于企业公开数据进行分析，包含工商信息、法律诉讼、资质合规、知识产权、舆情风险、经营风险等维度的综合评估。
            \n\n
            详细报告内容请下载后查看完整版本。
          </text>
        </view>
      </view>

      <!-- 操作按钮 -->
      <view class="action-buttons">
        <button class="action-btn primary" @click="downloadReport">
          <text class="btn-icon">📥</text>
          <text class="btn-text">下载报告</text>
        </button>
        <button class="action-btn secondary" @click="shareReport">
          <text class="btn-icon">📤</text>
          <text class="btn-text">分享报告</text>
        </button>
      </view>
    </view>

    <!-- 空状态 -->
    <view class="empty-state" v-if="!loading && !report">
      <text class="empty-text">报告不存在</text>
      <button class="back-btn" @click="goBack">返回</button>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      reportId: 0,
      report: null,
      loading: true
    };
  },
  
  onLoad(options) {
    if (options.id) {
      this.reportId = options.id;
      this.loadReport();
    }
  },
  
  methods: {
    // 加载报告详情
    async loadReport() {
      this.loading = true;
      try {
        // TODO: 实现报告详情 API
        // const res = await api.report.detail(this.reportId);
        // if (res.code === 200) {
        //   this.report = res.data;
        // }
        
        // 临时模拟数据
        this.report = {
          report_title: '企业风险分析报告',
          report_type: 1,
          company_name: '演示企业有限公司',
          created_at: new Date().toISOString(),
          download_count: 0,
          risk_score: 35,
          risk_level: 2,
          risk_count: 5
        };
      } catch (e) {
        console.error('加载失败', e);
        uni.showToast({ title: '加载失败', icon: 'none' });
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
      const h = String(date.getHours()).padStart(2, '0');
      const min = String(date.getMinutes()).padStart(2, '0');
      return `${y}-${m}-${d} ${h}:${min}`;
    },
    
    // 下载报告
    downloadReport() {
      uni.showLoading({ title: '生成报告中...' });
      
      setTimeout(() => {
        uni.hideLoading();
        uni.showToast({ title: '报告已下载', icon: 'success' });
      }, 1500);
    },
    
    // 分享报告
    shareReport() {
      uni.showActionSheet({
        itemList: ['分享给微信好友', '分享到朋友圈', '生成图片'],
        success: () => {
          uni.showToast({ title: '功能开发中', icon: 'none' });
        }
      });
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
  padding: 30rpx;
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

.report-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 24rpx;
  padding: 40rpx 30rpx;
  margin-bottom: 30rpx;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.report-title {
  font-size: 32rpx;
  font-weight: bold;
  color: #ffffff;
  flex: 1;
}

.report-type-badge {
  font-size: 22rpx;
  padding: 8rpx 20rpx;
  border-radius: 20rpx;
  background: rgba(255,255,255,0.2);
  color: #ffffff;
}

.report-info-card {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 20rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  font-size: 26rpx;
  color: #999;
}

.info-value {
  font-size: 26rpx;
  color: #333;
}

.score-card {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 40rpx 30rpx;
  display: flex;
  align-items: center;
  margin-bottom: 30rpx;
}

.score-circle {
  width: 160rpx;
  height: 160rpx;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-right: 40rpx;
  color: #ffffff;
}

.score-value {
  font-size: 56rpx;
  font-weight: bold;
}

.score-label {
  font-size: 22rpx;
  opacity: 0.8;
  margin-top: 5rpx;
}

.score-detail {
  display: flex;
  flex-direction: column;
}

.level-badge {
  font-size: 28rpx;
  padding: 10rpx 30rpx;
  border-radius: 30rpx;
  margin-bottom: 16rpx;
  display: inline-block;
}

.level-1 { background: #e6f7ff; color: #1890ff; }
.level-2 { background: #fff7e6; color: #fa8c16; }
.level-3 { background: #fff1f0; color: #f5222d; }

.risk-count {
  font-size: 26rpx;
  color: #999;
}

.content-section {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
}

.section-title {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
  display: block;
  margin-bottom: 24rpx;
}

.preview-content {
  background: #f9f9f9;
  border-radius: 12rpx;
  padding: 24rpx;
}

.preview-text {
  font-size: 26rpx;
  color: #666;
  line-height: 1.8;
  white-space: pre-wrap;
}

.action-buttons {
  margin-top: 40rpx;
}

.action-btn {
  width: 100%;
  height: 96rpx;
  border-radius: 16rpx;
  font-size: 30rpx;
  margin-bottom: 20rpx;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn.primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  border: none;
}

.action-btn.secondary {
  background: #ffffff;
  color: #667eea;
  border: 2rpx solid #667eea;
}

.btn-icon {
  font-size: 36rpx;
  margin-right: 12rpx;
}

.btn-text {
  font-weight: 500;
}
</style>
