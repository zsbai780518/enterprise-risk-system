<template>
  <view class="container">
    <!-- 用户信息头部 -->
    <view class="user-header" v-if="isLoggedIn">
      <view class="avatar" @click="editAvatar">
        <image v-if="userInfo.avatar" :src="userInfo.avatar" mode="aspectFill" />
        <text v-else class="avatar-text">{{ avatarText }}</text>
      </view>
      <view class="user-info">
        <text class="username">{{ userInfo.username || userInfo.phone || '未设置' }}</text>
        <text class="user-id">ID: {{ userInfo.user_id }}</text>
      </view>
      <view class="edit-btn" @click="editProfile">✏️</view>
    </view>

    <!-- 未登录提示 -->
    <view class="not-logged-in" v-if="!isLoggedIn">
      <text class="not-logged-text">您还未登录</text>
      <button class="login-btn" @click="goLogin">立即登录</button>
    </view>

    <!-- 功能菜单 -->
    <view class="menu-section" v-if="isLoggedIn">
      <view class="menu-group">
        <text class="group-title">我的企业</text>
        <view class="menu-item" @click="goMyCompanies">
          <text class="menu-icon">🏢</text>
          <text class="menu-text">企业管理</text>
          <text class="menu-arrow">›</text>
        </view>
        <view class="menu-item" @click="goRiskMonitor">
          <text class="menu-icon">🔔</text>
          <text class="menu-text">风险预警</text>
          <view class="menu-badge">
            <text class="menu-text">风险预警</text>
            <text class="badge" v-if="riskCount > 0">{{ riskCount }}</text>
          </view>
          <text class="menu-arrow">›</text>
        </view>
      </view>

      <view class="menu-group">
        <text class="group-title">我的服务</text>
        <view class="menu-item" @click="goReports">
          <text class="menu-icon">📋</text>
          <text class="menu-text">分析报告</text>
          <text class="menu-arrow">›</text>
        </view>
        <view class="menu-item" @click="goFavorites">
          <text class="menu-icon">⭐</text>
          <text class="menu-text">收藏企业</text>
          <text class="menu-arrow">›</text>
        </view>
        <view class="menu-item" @click="goHistory">
          <text class="menu-icon">📜</text>
          <text class="menu-text">查询历史</text>
          <text class="menu-arrow">›</text>
        </view>
      </view>

      <view class="menu-group">
        <text class="group-title">通用设置</text>
        <view class="menu-item" @click="goSettings">
          <text class="menu-icon">⚙️</text>
          <text class="menu-text">设置</text>
          <text class="menu-arrow">›</text>
        </view>
        <view class="menu-item" @click="goAbout">
          <text class="menu-icon">ℹ️</text>
          <text class="menu-text">关于我们</text>
          <text class="menu-arrow">›</text>
        </view>
        <view class="menu-item" @click="goFeedback">
          <text class="menu-icon">💬</text>
          <text class="menu-text">意见反馈</text>
          <text class="menu-arrow">›</text>
        </view>
      </view>
    </view>

    <!-- 退出登录按钮 -->
    <view class="logout-section" v-if="isLoggedIn">
      <button class="logout-btn" @click="handleLogout">退出登录</button>
    </view>

    <view class="version">
      <text>当前版本：v1.0.0</text>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      isLoggedIn: false,
      userInfo: {},
      riskCount: 0
    };
  },
  
  computed: {
    avatarText() {
      const name = this.userInfo.username || this.userInfo.phone || 'U';
      return name.charAt(0).toUpperCase();
    }
  },
  
  onLoad() {
    this.checkLogin();
  },
  
  onShow() {
    this.checkLogin();
  },
  
  methods: {
    // 检查登录状态
    checkLogin() {
      const userId = uni.getStorageSync('user_id');
      const token = uni.getStorageSync('token');
      
      if (userId && token) {
        this.isLoggedIn = true;
        this.userInfo = JSON.parse(uni.getStorageSync('user_info') || '{}');
        this.loadUserInfo();
      } else {
        this.isLoggedIn = false;
        this.userInfo = {};
      }
    },
    
    // 加载用户信息
    async loadUserInfo() {
      try {
        const res = await api.user.info();
        if (res.code === 200) {
          this.userInfo = res.data;
          uni.setStorageSync('user_info', JSON.stringify(res.data));
        }
      } catch (e) {
        console.log('加载用户信息失败', e);
      }
    },
    
    // 编辑头像
    editAvatar() {
      uni.chooseImage({
        count: 1,
        success: (res) => {
          const tempFilePath = res.tempFilePaths[0];
          // TODO: 上传头像
          uni.showToast({ title: '功能开发中', icon: 'none' });
        }
      });
    },
    
    // 编辑资料
    editProfile() {
      uni.navigateTo({ url: '/pages/mine/profile' });
    },
    
    // 跳转登录
    goLogin() {
      uni.navigateTo({ url: '/pages/mine/login' });
    },
    
    // 企业管理
    goMyCompanies() {
      uni.switchTab({ url: '/pages/company/list' });
    },
    
    // 风险预警
    goRiskMonitor() {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 分析报告
    goReports() {
      uni.switchTab({ url: '/pages/report/list' });
    },
    
    // 收藏企业
    goFavorites() {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 查询历史
    goHistory() {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 设置
    goSettings() {
      uni.navigateTo({ url: '/pages/mine/settings' });
    },
    
    // 关于我们
    goAbout() {
      uni.navigateTo({ url: '/pages/mine/about' });
    },
    
    // 意见反馈
    goFeedback() {
      uni.navigateTo({ url: '/pages/mine/feedback' });
    },
    
    // 退出登录
    async handleLogout() {
      uni.showModal({
        title: '确认退出',
        content: '确定要退出登录吗？',
        success: async (res) => {
          if (res.confirm) {
            try {
              await api.user.logout();
            } catch (e) {
              console.log('登出失败', e);
            }
            
            // 清除本地数据
            uni.removeStorageSync('token');
            uni.removeStorageSync('user_id');
            uni.removeStorageSync('user_info');
            
            this.isLoggedIn = false;
            this.userInfo = {};
            
            uni.showToast({ title: '已退出登录', icon: 'success' });
            
            // 返回首页
            setTimeout(() => {
              uni.switchTab({ url: '/pages/index/index' });
            }, 1000);
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

.user-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 60rpx 40rpx 40rpx;
  display: flex;
  align-items: center;
}

.avatar {
  width: 120rpx;
  height: 120rpx;
  border-radius: 50%;
  background: rgba(255,255,255,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.avatar image {
  width: 100%;
  height: 100%;
}

.avatar-text {
  font-size: 48rpx;
  color: #ffffff;
  font-weight: bold;
}

.user-info {
  flex: 1;
  margin-left: 30rpx;
}

.username {
  font-size: 36rpx;
  color: #ffffff;
  font-weight: bold;
  display: block;
}

.user-id {
  font-size: 24rpx;
  color: rgba(255,255,255,0.7);
  margin-top: 10rpx;
  display: block;
}

.edit-btn {
  font-size: 40rpx;
  padding: 10rpx;
}

.not-logged-in {
  background: #ffffff;
  padding: 80rpx 40rpx;
  text-align: center;
}

.not-logged-text {
  font-size: 28rpx;
  color: #999;
  display: block;
  margin-bottom: 30rpx;
}

.login-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  border: none;
  border-radius: 12rpx;
  height: 80rpx;
  font-size: 28rpx;
  width: 300rpx;
}

.menu-section {
  padding: 30rpx;
}

.menu-group {
  background: #ffffff;
  border-radius: 16rpx;
  padding: 20rpx 30rpx;
  margin-bottom: 30rpx;
}

.group-title {
  font-size: 24rpx;
  color: #999;
  display: block;
  margin-bottom: 20rpx;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 24rpx 0;
  border-bottom: 1rpx solid #f0f0f0;
}

.menu-item:last-child {
  border-bottom: none;
}

.menu-icon {
  font-size: 36rpx;
  margin-right: 20rpx;
}

.menu-text {
  flex: 1;
  font-size: 28rpx;
  color: #333;
}

.menu-badge {
  display: flex;
  align-items: center;
}

.badge {
  background: #f5222d;
  color: #ffffff;
  font-size: 20rpx;
  padding: 4rpx 12rpx;
  border-radius: 20rpx;
  margin-left: 16rpx;
}

.menu-arrow {
  font-size: 40rpx;
  color: #ccc;
}

.logout-section {
  padding: 30rpx 40rpx 60rpx;
}

.logout-btn {
  background: #ffffff;
  color: #f5222d;
  border: 2rpx solid #f5222d;
  border-radius: 12rpx;
  height: 88rpx;
  font-size: 28rpx;
}

.version {
  text-align: center;
  padding: 20rpx;
}

.version text {
  font-size: 24rpx;
  color: #ccc;
}
</style>
