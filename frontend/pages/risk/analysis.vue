<template>
  <view class="container">
    <!-- 加载状态 -->
    <view class="loading-state" v-if="loading">
      <text>分析中...</text>
    </view>

    <view v-else-if="analysis">
      <!-- 风险概览 -->
      <view class="overview-card">
        <view class="overview-header">
          <text class="company-name">{{ analysis.company_name }}</text>
          <text class="analysis-time">分析时间：{{ currentTime }}</text>
        </view>
        <view class="score-section">
          <view class="score-circle">
            <text class="score-value">{{ analysis.risk_score }}</text>
            <text class="score-label">风险评分</text>
          </view>
          <view class="score-info">
            <view class="level-badge" :class="'level-' + analysis.risk_level">
              {{ analysis.risk_level_text }}风险
            </view>
            <text class="total-risks">共 {{ analysis.total_risks }} 项风险</text>
          </view>
        </view>
      </view>

      <!-- 风险分布雷达图 -->
      <view class="chart-section">
        <text class="section-title">风险分布</text>
        <view class="chart-container">
          <canvas 
            canvas-id="riskRadar" 
            class="radar-chart"
            id="riskRadar"
          ></canvas>
        </view>
      </view>

      <!-- 关键风险点 -->
      <view class="risks-section">
        <text class="section-title">关键风险点（{{ analysis.key_risks?.length || 0 }}）</text>
        <view class="risk-list">
          <view 
            v-for="(item, index) in analysis.key_risks" 
            :key="index"
            class="risk-card"
          >
            <view class="risk-card-header">
              <view class="risk-type">{{ getRiskTypeText(item.risk_type) }}</view>
              <view class="risk-level-badge" :class="'risk-' + item.risk_level">
                {{ getRiskLevelText(item.risk_level) }}
              </view>
            </view>
            <text class="risk-card-title">{{ item.risk_title }}</text>
            <text class="risk-card-content">{{ item.risk_content || '暂无详情' }}</text>
            <text class="risk-card-source" v-if="item.risk_source">来源：{{ item.risk_source }}</text>
          </view>
        </view>
      </view>

      <!-- 资质问题 -->
      <view class="section" v-if="analysis.qualification_issues && analysis.qualification_issues.length > 0">
        <text class="section-title">资质问题（{{ analysis.qualification_issues.length }}）</text>
        <view class="issue-list">
          <view 
            v-for="(item, index) in analysis.qualification_issues" 
            :key="index"
            class="issue-item"
          >
            <text class="issue-icon">{{ item.type === 'expired' ? '⚠️' : '❌' }}</text>
            <view class="issue-info">
              <text class="issue-name">{{ item.name }}</text>
              <text class="issue-desc" v-if="item.valid_to">有效期至：{{ item.valid_to }}</text>
            </view>
            <text class="issue-type">{{ item.type === 'expired' ? '已过期' : '缺失' }}</text>
          </view>
        </view>
      </view>

      <!-- 建议措施 -->
      <view class="section" v-if="analysis.suggestions && analysis.suggestions.length > 0">
        <text class="section-title">💡 建议措施</text>
        <view class="suggestion-list">
          <view 
            v-for="(item, index) in analysis.suggestions" 
            :key="index"
            class="suggestion-item"
          >
            <text class="suggestion-num">{{ index + 1 }}</text>
            <text class="suggestion-text">{{ item }}</text>
          </view>
        </view>
      </view>

      <!-- 操作按钮 -->
      <view class="action-buttons">
        <button class="action-btn primary" @click="generateReport">生成完整报告</button>
        <button class="action-btn secondary" @click="exportData">导出数据</button>
      </view>
    </view>

    <!-- 空状态 -->
    <view class="empty-state" v-if="!loading && !analysis">
      <text class="empty-text">暂无分析数据</text>
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
      analysis: null,
      loading: true,
      currentTime: ''
    };
  },
  
  onLoad(options) {
    if (options.company_id) {
      this.companyId = options.company_id;
      this.loadAnalysis();
    }
  },
  
  onReady() {
    // 绘制雷达图
    if (this.analysis) {
      this.drawRadarChart();
    }
  },
  
  methods: {
    // 加载分析数据
    async loadAnalysis() {
      this.loading = true;
      try {
        const res = await api.risk.analysis(this.companyId);
        if (res.code === 200 && res.data) {
          this.analysis = res.data;
          this.currentTime = this.formatTime(new Date());
          
          // 延迟绘制图表
          setTimeout(() => {
            this.drawRadarChart();
          }, 300);
        } else {
          uni.showToast({ title: '加载失败', icon: 'none' });
        }
      } catch (e) {
        console.error('加载失败', e);
        uni.showToast({ title: '加载失败', icon: 'none' });
      } finally {
        this.loading = false;
      }
    },
    
    // 格式化时间
    formatTime(date) {
      const y = date.getFullYear();
      const m = String(date.getMonth() + 1).padStart(2, '0');
      const d = String(date.getDate()).padStart(2, '0');
      const h = String(date.getHours()).padStart(2, '0');
      const min = String(date.getMinutes()).padStart(2, '0');
      return `${y}-${m}-${d} ${h}:${min}`;
    },
    
    // 获取风险等级文本
    getRiskLevelText(level) {
      const map = { 1: '低', 2: '中', 3: '高' };
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
    
    // 绘制雷达图
    drawRadarChart() {
      const ctx = uni.createCanvasContext('riskRadar', this);
      const radarData = this.analysis.risk_distribution?.type || {};
      
      // 雷达图参数
      const centerX = 150;
      const centerY = 150;
      const radius = 100;
      const angles = [0, 60, 120, 180, 240, 300]; // 6 个维度
      const labels = ['工商', '法律诉讼', '资质', '知识产权', '舆情', '经营'];
      const values = [
        radarData[1] || 0,
        radarData[2] || 0,
        radarData[3] || 0,
        radarData[4] || 0,
        radarData[5] || 0,
        radarData[6] || 0
      ];
      
      // 找最大值用于缩放
      const maxValue = Math.max(...values, 1);
      
      // 绘制背景网格
      ctx.setStrokeStyle('#e0e0e0');
      ctx.setLineWidth(1);
      for (let i = 1; i <= 5; i++) {
        const r = (radius / 5) * i;
        ctx.beginPath();
        for (let j = 0; j < 6; j++) {
          const angle = (angles[j] * Math.PI) / 180 - Math.PI / 2;
          const x = centerX + r * Math.cos(angle);
          const y = centerY + r * Math.sin(angle);
          if (j === 0) {
            ctx.moveTo(x, y);
          } else {
            ctx.lineTo(x, y);
          }
        }
        ctx.closePath();
        ctx.stroke();
      }
      
      // 绘制轴线
      for (let i = 0; i < 6; i++) {
        const angle = (angles[i] * Math.PI) / 180 - Math.PI / 2;
        const x = centerX + radius * Math.cos(angle);
        const y = centerY + radius * Math.sin(angle);
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(x, y);
        ctx.stroke();
      }
      
      // 绘制数据区域
      ctx.setFillStyle('rgba(102, 126, 234, 0.3)');
      ctx.setStrokeStyle('#667eea');
      ctx.setLineWidth(2);
      ctx.beginPath();
      for (let i = 0; i < 6; i++) {
        const angle = (angles[i] * Math.PI) / 180 - Math.PI / 2;
        const normalizedValue = (values[i] / maxValue) * radius;
        const x = centerX + normalizedValue * Math.cos(angle);
        const y = centerY + normalizedValue * Math.sin(angle);
        if (i === 0) {
          ctx.moveTo(x, y);
        } else {
          ctx.lineTo(x, y);
        }
      }
      ctx.closePath();
      ctx.fill();
      ctx.stroke();
      
      // 绘制数据点
      ctx.setFillStyle('#667eea');
      for (let i = 0; i < 6; i++) {
        const angle = (angles[i] * Math.PI) / 180 - Math.PI / 2;
        const normalizedValue = (values[i] / maxValue) * radius;
        const x = centerX + normalizedValue * Math.cos(angle);
        const y = centerY + normalizedValue * Math.sin(angle);
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, 2 * Math.PI);
        ctx.fill();
      }
      
      // 绘制标签
      ctx.setFontSize(12);
      ctx.setFillStyle('#666');
      ctx.setTextAlign('center');
      for (let i = 0; i < 6; i++) {
        const angle = (angles[i] * Math.PI) / 180 - Math.PI / 2;
        const labelX = centerX + (radius + 20) * Math.cos(angle);
        const labelY = centerY + (radius + 20) * Math.sin(angle);
        ctx.fillText(labels[i], labelX, labelY);
      }
      
      ctx.draw();
    },
    
    // 生成报告
    generateReport() {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 导出数据
    exportData() {
      uni.showToast({ title: '功能开发中', icon: 'none' });
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

.overview-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 24rpx;
  padding: 40rpx 30rpx;
  margin-bottom: 30rpx;
  color: #ffffff;
}

.overview-header {
  margin-bottom: 30rpx;
}

.company-name {
  font-size: 32rpx;
  font-weight: bold;
  display: block;
}

.analysis-time {
  font-size: 22rpx;
  opacity: 0.8;
  display: block;
  margin-top: 10rpx;
}

.score-section {
  display: flex;
  align-items: center;
}

.score-circle {
  width: 140rpx;
  height: 140rpx;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-right: 40rpx;
}

.score-value {
  font-size: 48rpx;
  font-weight: bold;
}

.score-label {
  font-size: 20rpx;
  opacity: 0.8;
  margin-top: 5rpx;
}

.score-info {
  display: flex;
  flex-direction: column;
}

.level-badge {
  font-size: 28rpx;
  padding: 10rpx 30rpx;
  border-radius: 30rpx;
  background: rgba(255,255,255,0.2);
  margin-bottom: 16rpx;
  display: inline-block;
}

.total-risks {
  font-size: 24rpx;
  opacity: 0.8;
}

.chart-section {
  background: #ffffff;
  border-radius: 24rpx;
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

.chart-container {
  display: flex;
  justify-content: center;
}

.radar-chart {
  width: 300rpx;
  height: 300rpx;
}

.risks-section {
  background: #ffffff;
  border-radius: 24rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
}

.risk-list {
  border-top: 1rpx solid #f0f0f0;
}

.risk-card {
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.risk-card-header {
  display: flex;
  align-items: center;
  margin-bottom: 12rpx;
}

.risk-type {
  font-size: 22rpx;
  padding: 4rpx 12rpx;
  border-radius: 8rpx;
  background: #f5f5f5;
  color: #666;
  margin-right: 16rpx;
}

.risk-level-badge {
  font-size: 20rpx;
  padding: 4rpx 12rpx;
  border-radius: 12rpx;
}

.risk-1 { background: #e6f7ff; color: #1890ff; }
.risk-2 { background: #fff7e6; color: #fa8c16; }
.risk-3 { background: #fff1f0; color: #f5222d; }

.risk-card-title {
  font-size: 28rpx;
  color: #333;
  font-weight: 500;
  display: block;
  margin-bottom: 10rpx;
}

.risk-card-content {
  font-size: 24rpx;
  color: #666;
  display: block;
  margin-bottom: 10rpx;
}

.risk-card-source {
  font-size: 22rpx;
  color: #999;
  display: block;
}

.section {
  background: #ffffff;
  border-radius: 24rpx;
  padding: 30rpx;
  margin-bottom: 30rpx;
}

.issue-list, .suggestion-list {
  border-top: 1rpx solid #f0f0f0;
}

.issue-item {
  display: flex;
  align-items: center;
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.issue-icon {
  font-size: 36rpx;
  margin-right: 16rpx;
}

.issue-info {
  flex: 1;
}

.issue-name {
  font-size: 28rpx;
  color: #333;
  display: block;
}

.issue-desc {
  font-size: 22rpx;
  color: #999;
  display: block;
  margin-top: 6rpx;
}

.issue-type {
  font-size: 24rpx;
  padding: 6rpx 16rpx;
  border-radius: 12rpx;
  background: #fff1f0;
  color: #f5222d;
}

.suggestion-item {
  display: flex;
  align-items: flex-start;
  padding: 20rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.suggestion-num {
  width: 40rpx;
  height: 40rpx;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  font-size: 24rpx;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 16rpx;
  flex-shrink: 0;
}

.suggestion-text {
  font-size: 26rpx;
  color: #333;
  flex: 1;
  line-height: 1.6;
}

.action-buttons {
  margin-top: 40rpx;
}

.action-btn {
  width: 100%;
  height: 88rpx;
  border-radius: 12rpx;
  font-size: 28rpx;
  margin-bottom: 20rpx;
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
</style>
