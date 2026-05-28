<template>
  <view class="container">
    <view class="logo-banner">
      <text class="logo-text">校园二手书</text>
      <text class="logo-sub">大学生专属教材交易平台</text>
    </view>

    <view class="card form-card">
      <view class="input-group">
        <text class="input-label">手机号</text>
        <input v-model="form.phone" type="number" maxlength="11" placeholder="请输入11位手机号" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">密码</text>
        <input v-model="form.password" type="password" placeholder="请输入密码" class="input" />
      </view>
      <view class="btn-amber login-btn" @click="onLogin">登 录</view>
      <view class="link-row">
        <text class="link" @click="goRegister">没有账号？去注册</text>
        <text class="link" @click="goAdmin">管理后台</text>
      </view>
    </view>
  </view>
</template>

<script>
import { post } from '@/utils/request.js'
import auth from '@/stores/auth.js'

export default {
  data() {
    return {
      form: { phone: '', password: '' }
    }
  },
  methods: {
    async onLogin() {
      if (!this.form.phone || this.form.phone.length !== 11) {
        uni.showToast({ title: '请输入正确的手机号', icon: 'none' })
        return
      }
      if (!this.form.password) {
        uni.showToast({ title: '请输入密码', icon: 'none' })
        return
      }
      try {
        var res = await post('/api/auth/login', this.form)
        auth.save(res.data.token, res.data.user)
        uni.showToast({ title: '登录成功', icon: 'success' })
        setTimeout(function () {
          uni.switchTab({ url: '/pages/index/index' })
        }, 800)
      } catch (e) {
        console.log('login error:', e)
      }
    },
    goRegister() { uni.navigateTo({ url: '/pages/auth/register' }) },
    goAdmin() {
      uni.showToast({ title: '请在电脑端访问管理后台', icon: 'none' })
    }
  }
}
</script>

<style scoped>
.container { min-height: 100vh; background: linear-gradient(135deg, #b49450 0%, #b49450 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
.logo-banner { text-align: center; margin-bottom: 32px; }
.logo-text { font-size: 28px; font-weight: 700; color: #fff; display: block; }
.logo-sub { font-size: 14px; color: rgba(255,255,255,0.7); margin-top: 8px; display: block; }
.form-card { width: 100%; max-width: 360px; padding: 24px; }
.input-group { margin-bottom: 16px; }
.input-label { font-size: 14px; color: #8c8478; margin-bottom: 6px; display: block; }
.input { width: 100%; height: 44px; border: 1px solid #e5dccf; border-radius: 8px; padding: 0 12px; font-size: 14px; background: #fff; box-sizing: border-box; }
.login-btn { width: 100%; text-align: center; margin-top: 20px; padding: 13px 0; font-size: 16px; border-radius: 8px; }
.link-row { display: flex; justify-content: space-between; margin-top: 16px; }
.link { font-size: 13px; color: #b49450; }
</style>
