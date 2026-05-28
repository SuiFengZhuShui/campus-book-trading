<template>
  <view class="container">
    <view class="logo-banner">
      <text class="logo-text">校园二手书</text>
      <text class="logo-sub">大学生专属教材交易平台</text>
    </view>

    <view class="card form-card">
      <view class="input-group">
        <text class="input-label">姓名</text>
        <input v-model="form.name" placeholder="请输入真实姓名" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">学号</text>
        <input v-model="form.student_id" placeholder="请输入学号" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">手机号</text>
        <input v-model="form.phone" type="number" maxlength="11" placeholder="请输入11位手机号" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">密码</text>
        <input v-model="form.password" type="password" placeholder="8位以上，含大小写字母和数字" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">确认密码</text>
        <input v-model="form.password_confirmation" type="password" placeholder="请再次输入密码" class="input" />
      </view>
      <view class="btn-amber login-btn" @click="onRegister">注 册</view>
      <view class="link-row">
        <text class="link" @click="goLogin">已有账号？去登录</text>
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
      form: {
        name: '',
        student_id: '',
        phone: '',
        password: '',
        password_confirmation: ''
      }
    }
  },
  methods: {
    async onRegister() {
      if (!this.form.name.trim()) { uni.showToast({ title: '请输入姓名', icon: 'none' }); return }
      if (!this.form.student_id.trim()) { uni.showToast({ title: '请输入学号', icon: 'none' }); return }
      if (!this.form.phone || this.form.phone.length !== 11) { uni.showToast({ title: '请输入正确的手机号', icon: 'none' }); return }
      if (!this.form.password || this.form.password.length < 8) { uni.showToast({ title: '密码至少8位', icon: 'none' }); return }
      if (this.form.password !== this.form.password_confirmation) { uni.showToast({ title: '两次密码不一致', icon: 'none' }); return }

      try {
        var res = await post('/api/auth/register', this.form)
        auth.save(res.data.token, res.data.user)
        uni.showToast({ title: '注册成功', icon: 'success' })
        setTimeout(function () {
          uni.switchTab({ url: '/pages/index/index' })
        }, 800)
      } catch (e) {
        console.log('register error:', e)
      }
    },
    goLogin() { uni.navigateTo({ url: '/pages/auth/login' }) }
  }
}
</script>

<style scoped>
.container { min-height: 100vh; background: linear-gradient(135deg, #b49450 0%, #b49450 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
.logo-banner { text-align: center; margin-bottom: 24px; }
.logo-text { font-size: 28px; font-weight: 700; color: #fff; display: block; }
.logo-sub { font-size: 14px; color: rgba(255,255,255,0.7); margin-top: 8px; display: block; }
.form-card { width: 100%; max-width: 360px; padding: 24px; }
.input-group { margin-bottom: 14px; }
.input-label { font-size: 14px; color: #8c8478; margin-bottom: 6px; display: block; }
.input { width: 100%; height: 44px; border: 1px solid #e5dccf; border-radius: 8px; padding: 0 12px; font-size: 14px; background: #fff; box-sizing: border-box; }
.login-btn { width: 100%; text-align: center; margin-top: 20px; padding: 13px 0; font-size: 16px; border-radius: 8px; }
.link-row { display: flex; justify-content: center; margin-top: 16px; }
.link { font-size: 13px; color: #b49450; }
</style>
