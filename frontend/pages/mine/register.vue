<template>
  <view class="container">
    <view class="header">
      <text class="title">注册账号</text>
      <text class="subtitle">已有账号？<text class="login-link" @click="goLogin">立即登录</text></text>
    </view>

    <view class="form-section">
      <!-- 注册方式选择 -->
      <view class="tab-group">
        <view 
          class="tab-item" 
          :class="{ active: registerType === 'phone' }"
          @click="registerType = 'phone'"
        >
          手机号注册
        </view>
        <view 
          class="tab-item" 
          :class="{ active: registerType === 'username' }"
          @click="registerType = 'username'"
        >
          用户名注册
        </view>
      </view>

      <!-- 手机号输入 -->
      <view class="input-group" v-if="registerType === 'phone'">
        <view class="input-wrapper">
          <text class="input-icon">📱</text>
          <input 
            class="input" 
            v-model="phone" 
            type="number"
            maxlength="11"
            placeholder="请输入手机号"
            placeholder-class="input-placeholder"
          />
        </view>
      </view>

      <!-- 验证码（手机号注册时显示） -->
      <view class="input-group" v-if="registerType === 'phone'">
        <view class="input-wrapper">
          <text class="input-icon">🔐</text>
          <input 
            class="input" 
            v-model="code" 
            type="number"
            maxlength="6"
            placeholder="请输入验证码"
            placeholder-class="input-placeholder"
          />
          <text class="code-btn" :class="{ disabled: codeDisabled }" @click="sendCode">
            {{ codeText }}
          </text>
        </view>
      </view>

      <!-- 用户名输入 -->
      <view class="input-group" v-if="registerType === 'username'">
        <view class="input-wrapper">
          <text class="input-icon">👤</text>
          <input 
            class="input" 
            v-model="username" 
            placeholder="请输入用户名（6-20 位）"
            placeholder-class="input-placeholder"
          />
        </view>
      </view>

      <!-- 密码输入 -->
      <view class="input-group">
        <view class="input-wrapper">
          <text class="input-icon">🔒</text>
          <input 
            class="input" 
            v-model="password" 
            type="password"
            placeholder="请输入密码（6-20 位）"
            placeholder-class="input-placeholder"
          />
        </view>
      </view>

      <!-- 确认密码 -->
      <view class="input-group">
        <view class="input-wrapper">
          <text class="input-icon">🔒</text>
          <input 
            class="input" 
            v-model="confirmPassword" 
            type="password"
            placeholder="请确认密码"
            placeholder-class="input-placeholder"
          />
        </view>
      </view>

      <!-- 协议勾选 -->
      <view class="agreement">
        <checkbox :checked="agreed" @change="onAgreeChange" />
        <text class="agreement-text">
          我已阅读并同意
          <text class="link" @click="viewAgreement('user')">《用户协议》</text>
          和
          <text class="link" @click="viewAgreement('privacy')">《隐私政策》</text>
        </text>
      </view>

      <button class="register-btn" @click="handleRegister" :loading="loading">
        {{ loading ? '注册中...' : '立即注册' }}
      </button>
    </view>

    <view class="footer">
      <text class="footer-text">注册即代表您同意我们的服务条款</text>
    </view>
  </view>
</template>

<script>
import api from '@/api/index.js';

export default {
  data() {
    return {
      registerType: 'phone', // phone | username
      phone: '',
      code: '',
      username: '',
      password: '',
      confirmPassword: '',
      agreed: false,
      loading: false,
      codeDisabled: false,
      codeText: '获取验证码',
      codeTimer: null
    };
  },
  
  onUnload() {
    if (this.codeTimer) {
      clearInterval(this.codeTimer);
    }
  },
  
  methods: {
    // 发送验证码
    sendCode() {
      if (this.codeDisabled) return;
      
      if (!this.phone || !/^1[3-9]\d{9}$/.test(this.phone)) {
        uni.showToast({ title: '请输入正确的手机号', icon: 'none' });
        return;
      }

      // 模拟发送验证码
      uni.showToast({ title: '验证码已发送', icon: 'success' });
      
      // 倒计时
      let seconds = 60;
      this.codeDisabled = true;
      this.codeText = `${seconds}s 后重发`;
      
      this.codeTimer = setInterval(() => {
        seconds--;
        if (seconds <= 0) {
          clearInterval(this.codeTimer);
          this.codeDisabled = false;
          this.codeText = '获取验证码';
        } else {
          this.codeText = `${seconds}s 后重发`;
        }
      }, 1000);
    },
    
    // 协议勾选
    onAgreeChange(e) {
      this.agreed = e.detail.value;
    },
    
    // 查看协议
    viewAgreement(type) {
      uni.showToast({ title: '功能开发中', icon: 'none' });
    },
    
    // 处理注册
    async handleRegister() {
      // 验证协议
      if (!this.agreed) {
        uni.showToast({ title: '请先同意用户协议', icon: 'none' });
        return;
      }

      // 验证密码
      if (!this.password || this.password.length < 6) {
        uni.showToast({ title: '密码长度不能少于 6 位', icon: 'none' });
        return;
      }
      if (this.password !== this.confirmPassword) {
        uni.showToast({ title: '两次输入的密码不一致', icon: 'none' });
        return;
      }

      // 准备注册数据
      const data = { password: this.password };
      
      if (this.registerType === 'phone') {
        if (!this.phone || !/^1[3-9]\d{9}$/.test(this.phone)) {
          uni.showToast({ title: '请输入正确的手机号', icon: 'none' });
          return;
        }
        if (!this.code || this.code.length !== 6) {
          uni.showToast({ title: '请输入 6 位验证码', icon: 'none' });
          return;
        }
        data.phone = this.phone;
        data.code = this.code;
      } else {
        if (!this.username || this.username.length < 6) {
          uni.showToast({ title: '用户名长度不能少于 6 位', icon: 'none' });
          return;
        }
        data.username = this.username.trim();
      }

      this.loading = true;

      try {
        const res = await api.user.register(data);

        if (res.code === 200) {
          uni.showToast({ title: '注册成功', icon: 'success' });
          setTimeout(() => {
            uni.navigateTo({ url: '/pages/mine/login' });
          }, 1000);
        } else {
          uni.showToast({ title: res.msg || '注册失败', icon: 'none' });
        }
      } catch (e) {
        console.error('注册失败', e);
        uni.showToast({ title: '网络错误，请稍后重试', icon: 'none' });
      } finally {
        this.loading = false;
      }
    },
    
    // 跳转登录
    goLogin() {
      uni.navigateTo({ url: '/pages/mine/login' });
    }
  }
};
</script>

<style scoped>
.container {
  min-height: 100vh;
  background: #f5f5f5;
  padding: 40rpx;
}

.header {
  margin-bottom: 40rpx;
}

.title {
  font-size: 40rpx;
  font-weight: bold;
  color: #333;
  display: block;
}

.subtitle {
  font-size: 26rpx;
  color: #999;
  margin-top: 16rpx;
  display: block;
}

.login-link {
  color: #667eea;
  font-weight: 500;
}

.form-section {
  background: #ffffff;
  border-radius: 24rpx;
  padding: 40rpx;
}

.tab-group {
  display: flex;
  margin-bottom: 40rpx;
  border-bottom: 2rpx solid #f0f0f0;
}

.tab-item {
  flex: 1;
  text-align: center;
  padding: 20rpx 0;
  font-size: 28rpx;
  color: #666;
  border-bottom: 4rpx solid transparent;
  margin-bottom: -2rpx;
}

.tab-item.active {
  color: #667eea;
  border-bottom-color: #667eea;
  font-weight: 500;
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

.code-btn {
  font-size: 24rpx;
  color: #667eea;
  padding: 10rpx 20rpx;
  background: rgba(102, 126, 234, 0.1);
  border-radius: 8rpx;
  white-space: nowrap;
}

.code-btn.disabled {
  color: #999;
  background: #f0f0f0;
}

.agreement {
  display: flex;
  align-items: flex-start;
  margin: 30rpx 0;
}

.agreement-text {
  font-size: 24rpx;
  color: #666;
  flex: 1;
  line-height: 1.6;
}

.link {
  color: #667eea;
  text-decoration: underline;
}

.register-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #ffffff;
  border: none;
  border-radius: 12rpx;
  height: 96rpx;
  font-size: 32rpx;
  font-weight: bold;
  margin-top: 20rpx;
}

.footer {
  text-align: center;
  margin-top: 40rpx;
}

.footer-text {
  font-size: 24rpx;
  color: #999;
}
</style>
