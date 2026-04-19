<template>
  <view class="container">
    <view class="logo-section">
      <view class="logo">📊</view>
      <text class="app-name">企业风险分析系统</text>
    </view>

    <view class="form-section">
      <view class="input-group">
        <view class="input-wrapper">
          <text class="input-icon">📱</text>
          <input 
            class="input" 
            v-model="account" 
            placeholder="请输入手机号/用户名"
            placeholder-class="input-placeholder"
          />
        </view>
      </view>

      <view class="input-group">
        <view class="input-wrapper">
          <text class="input-icon">🔒</text>
          <input 
            class="input" 
            v-model="password" 
            type="password"
            placeholder="请输入密码"
            placeholder-class="input-placeholder"
          />
        </view>
      </view>

      <view class="actions">
        <view class="remember-me">
          <checkbox :checked="rememberMe" @change="onRememberChange" />
          <text class="remember-text">记住密码</text>
        </view>
        <text class="forget-password" @click="goForget">忘记密码？</text>
      </view>

      <button class="login-btn" @click="handleLogin" :loading="loading">
        {{ loading ? '登录中...' : '登录' }}
      </button>

      <view class="other-login">
        <text class="other-text">其他登录方式</text>
        <view class="other-icons">
          <view class="other-icon" @click="wechatLogin">💬</view>
        </view>
      </view>
    </view>

    <view class="footer-section">
      <text class="footer-text">还没有账号？</text>
      <text class="register-link" @click="goRegister">立即注册</text>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      account: '',
      password: '',
      rememberMe: false,
      loading: false
    };
  },
  
  onLoad() {
    // 检查是否有记住的账号
    const savedAccount = uni.getStorageSync('remembered_account');
    if (savedAccount) {
      this.account = savedAccount;
      this.rememberMe = true;
    }
  },
  
  methods: {
    // 记住密码
    onRememberChange(e) {
      this.rememberMe = e.detail.value;
    },
    
    // 处理登录
    async handleLogin() {
      // 验证输入
      if (!this.account.trim()) {
        uni.showToast({ title: '请输入账号', icon: 'none' });
        return;
      }
      if (!this.password) {
        uni.showToast({ title: '请输入密码', icon: 'none' });
        return;
      }

      this.loading = true;

      try {
        const res = await api.user.login({
          account: this.account.trim(),
          password: this.password
        });

        if (res.code === 200) {
          // 保存 token
          uni.setStorageSync('token', res.data.token);
          uni.setStorageSync('user_id', res.data.user_id);
          uni.setStorageSync('user_info', JSON.stringify(res.data));

          // 记住账号
          if (this.rememberMe) {
            uni.setStorageSync('remembered_account', this.account);
          } else {
            uni.removeStorageSync('remembered_account');
          }

          uni.showToast({ title: '登录成功', icon: 'success' });
          
          // 延迟跳转
          setTimeout(() => {
            uni.switchTab({ url: '/pages/index/index' });
          }, 1000);
        } else {
          uni.showToast({ title: res.msg || '登录失败', icon: 'none' });
        }
      } catch (e) {
        console.error('登录失败', e);
        uni.showToast({ title: '网络错误，请稍后重试', icon: 'none' });
      } finally {
        this.loading = false;
      }
    },
    
    // 微信登录
    wechatLogin() {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 忘记密码
    goForget() {
      uni.showToast({ title: '请联系客服重置密码', icon: 'none' });
    },
    
    // 跳转注册
    goRegister() {
      uni.navigateTo({ url: '/pages/mine/register' });
    }
  }
};
</script>

<style scoped>
.container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 60rpx 40rpx;
  display: flex;
  flex-direction: column;
}

.logo-section {
  text-align: center;
  margin-bottom: 80rpx;
}

.logo {
  font-size: 100rpx;
  margin-bottom: 20rpx;
}

.app-name {
  font-size: 36rpx;
  color: #ffffff;
  font-weight: bold;
}

.form-section {
  background: #ffffff;
  border-radius: 24rpx;
  padding: 50rpx 40rpx;
  flex: 1;
}

.input-group {
  margin-bottom: 30rpx;
}

.input-wrapper {
  display: flex;
  align-items: center;
  background: #f5f5f5;
  border-radius: 12rpx;
  padding: 0 24rpx;
  height: 96rpx;
}

.input-icon {
  font-size: 36rpx;
  margin-right: 16rpx;
}

.input {
  flex: 1;
  height: 100%;
  font-size: 28rpx;
}

.input-placeholder {
  color: #cccccc;
}

.actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 40rpx;
}

.remember-me {
  display: flex;
  align-items: center;
}

.remember-text {
  font-size: 24rpx;
  color: #666;
  margin-left: 10rpx;
}

.forget-password {
  font-size: 24rpx;
  color: #667eea;
}

.login-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  border: none;
  border-radius: 12rpx;
  height: 96rpx;
  font-size: 32rpx;
  font-weight: bold;
  margin-bottom: 40rpx;
}

.other-login {
  text-align: center;
  padding-top: 30rpx;
  border-top: 1rpx solid #f0f0f0;
}

.other-text {
  font-size: 24rpx;
  color: #999;
}

.other-icons {
  display: flex;
  justify-content: center;
  margin-top: 20rpx;
}

.other-icon {
  font-size: 48rpx;
  margin: 0 20rpx;
}

.footer-section {
  text-align: center;
  margin-top: 60rpx;
  display: flex;
  justify-content: center;
  align-items: center;
}

.footer-text {
  font-size: 26rpx;
  color: rgba(255,255,255,0.8);
}

.register-link {
  font-size: 26rpx;
  color: #ffffff;
  font-weight: bold;
  margin-left: 10rpx;
}
</style>
