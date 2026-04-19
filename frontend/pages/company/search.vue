<template>
  <view class="container">
    <!-- 搜索框 -->
    <view class="search-section">
      <view class="search-box">
        <input 
          class="search-input" 
          v-model="keyword"
          placeholder="输入企业名称或统一社会信用代码"
          @confirm="handleSearch"
        />
        <button class="search-btn" @click="handleSearch" :loading="searching">搜索</button>
      </view>
    </view>

    <!-- 搜索结果 -->
    <view class="result-section" v-if="searchResults.length > 0">
      <text class="section-title">搜索结果</text>
      <view 
        v-for="item in searchResults" 
        :key="item.id" 
        class="result-item"
        @click="selectCompany(item)"
      >
        <view class="result-info">
          <text class="result-name">{{ item.name }}</text>
          <text class="result-code">{{ item.unified_social_code }}</text>
          <text class="result-legal">法人：{{ item.legal_representative }}</text>
        </view>
        <view class="result-action">
          <text class="select-btn">选择</text>
        </view>
      </view>
    </view>

    <!-- 空状态 -->
    <view class="empty-state" v-if="!searching && searchResults.length === 0 && hasSearched">
      <text class="empty-text">未找到相关企业</text>
      <text class="empty-hint">请检查关键词后重试</text>
    </view>

    <!-- 初始提示 -->
    <view class="empty-state" v-if="!searching && searchResults.length === 0 && !hasSearched">
      <text class="empty-icon">🔍</text>
      <text class="empty-text">搜索企业</text>
      <text class="empty-hint">输入企业名称或统一社会信用代码进行搜索</text>
    </view>

    <!-- 加载状态 -->
    <view class="loading-state" v-if="searching">
      <text>搜索中...</text>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      keyword: '',
      searchResults: [],
      searching: false,
      hasSearched: false,
      type: 'search' // search | collect
    };
  },
  
  onLoad(options) {
    if (options.type) {
      this.type = options.type;
    }
  },
  
  methods: {
    // 处理搜索
    async handleSearch() {
      if (!this.keyword.trim()) {
        uni.showToast({ title: '请输入搜索关键词', icon: 'none' });
        return;
      }

      this.searching = true;
      this.hasSearched = true;

      try {
        const res = await api.company.search({ keyword: this.keyword.trim() });
        if (res.code === 200) {
          this.searchResults = res.data || [];
          if (this.searchResults.length === 0) {
            uni.showToast({ title: '未找到相关企业', icon: 'none' });
          }
        }
      } catch (e) {
        console.error('搜索失败', e);
        uni.showToast({ title: '搜索失败，请稍后重试', icon: 'none' });
      } finally {
        this.searching = false;
      }
    },
    
    // 选择企业
    selectCompany(item) {
      if (this.type === 'collect') {
        // 采集模式
        this.collectCompany(item);
      } else {
        // 搜索模式 - 先绑定再跳转
        this.bindAndGo(item);
      }
    },
    
    // 采集企业
    async collectCompany(item) {
      uni.showModal({
        title: '确认采集',
        content: `确定要采集 "${item.name}" 的企业数据吗？`,
        success: async (res) => {
          if (res.confirm) {
            try {
              const result = await api.company.collect({
                unified_social_code: item.unified_social_code
              });
              if (result.code === 200) {
                uni.showToast({ title: '采集成功', icon: 'success' });
                setTimeout(() => {
                  uni.switchTab({ url: '/pages/company/list' });
                }, 1500);
              } else {
                uni.showToast({ title: result.msg, icon: 'none' });
              }
            } catch (e) {
              uni.showToast({ title: '采集失败', icon: 'none' });
            }
          }
        }
      });
    },
    
    // 绑定并跳转
    async bindAndGo(item) {
      try {
        const result = await api.company.bind(item.id);
        if (result.code === 200) {
          uni.showToast({ title: '绑定成功', icon: 'success' });
          setTimeout(() => {
            uni.navigateTo({ url: '/pages/company/detail?id=' + item.id });
          }, 1000);
        } else {
          uni.showToast({ title: result.msg, icon: 'none' });
        }
      } catch (e) {
        uni.showToast({ title: '绑定失败', icon: 'none' });
      }
    }
  }
};
</script>

<style scoped>
.container {
  min-height: 100vh;
  background: #f5f5f5;
}

.search-section {
  background: #ffffff;
  padding: 30rpx;
}

.search-box {
  display: flex;
  align-items: center;
}

.search-input {
  flex: 1;
  background: #f5f5f5;
  border-radius: 12rpx;
  padding: 20rpx 24rpx;
  font-size: 28rpx;
  margin-right: 20rpx;
}

.search-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  border: none;
  border-radius: 12rpx;
  padding: 0 40rpx;
  height: 88rpx;
  font-size: 28rpx;
}

.result-section {
  padding: 30rpx;
}

.section-title {
  font-size: 28rpx;
  color: #999;
  display: block;
  margin-bottom: 20rpx;
}

.result-item {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 30rpx;
  margin-bottom: 20rpx;
  display: flex;
  align-items: center;
}

.result-info {
  flex: 1;
}

.result-name {
  font-size: 30rpx;
  font-weight: bold;
  color: #333;
  display: block;
}

.result-code {
  font-size: 24rpx;
  color: #999;
  display: block;
  margin-top: 10rpx;
}

.result-legal {
  font-size: 24rpx;
  color: #666;
  display: block;
  margin-top: 10rpx;
}

.result-action {
  margin-left: 20rpx;
}

.select-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  padding: 16rpx 32rpx;
  border-radius: 12rpx;
  font-size: 26rpx;
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
}

.loading-state {
  text-align: center;
  padding: 60rpx;
  color: #999;
}
</style>
