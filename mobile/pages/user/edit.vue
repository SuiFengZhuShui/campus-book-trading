<template>
  <view class="container">
    <view class="card form-card card-top-amber">
      <view class="input-group">
        <text class="input-label">姓名 <text class="required">*</text></text>
        <input v-model="form.name" placeholder="请输入姓名" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">学号 <text class="required">*</text></text>
        <input v-model="form.student_id" placeholder="请输入学号" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">手机号 <text class="required">*</text></text>
        <input v-model="form.phone" type="number" maxlength="11" placeholder="请输入11位手机号" class="input" />
      </view>

      <view class="btn-amber submit-btn" @click="onSubmit">保存</view>
    </view>
  </view>
</template>

<script>
import auth from '@/stores/auth.js'
import { post } from '@/utils/request.js'

export default {
  data() {
    return {
      form: {
        name: '',
        student_id: '',
        phone: ''
      }
    }
  },
  mounted: function () {
    var user = auth.user
    if (user) {
      this.form.name = user.name || ''
      this.form.student_id = user.student_id || ''
      this.form.phone = user.phone || ''
    }
  },
  methods: {
    onSubmit: async function () {
      if (!this.form.name.trim()) { uni.showToast({ title: '请输入姓名', icon: 'none' }); return }
      if (!this.form.student_id.trim()) { uni.showToast({ title: '请输入学号', icon: 'none' }); return }
      var phone = this.form.phone.trim()
      if (!phone) { uni.showToast({ title: '请输入手机号', icon: 'none' }); return }
      if (!/^\d{11}$/.test(phone)) { uni.showToast({ title: '手机号格式不正确', icon: 'none' }); return }

      uni.showLoading({ title: '保存中...' })
      try {
        var res = await post('/api/auth/profile', {
          name: this.form.name.trim(),
          student_id: this.form.student_id.trim(),
          phone: phone
        })
        uni.hideLoading()
        // 更新本地 auth store
        if (auth.user) {
          auth.user.name = res.data.name
          auth.user.student_id = res.data.student_id
          auth.user.phone = res.data.phone
          auth.save(auth.token, auth.user)
        }
        uni.showToast({ title: '保存成功', icon: 'success' })
        setTimeout(function () {
          uni.navigateBack()
        }, 1000)
      } catch (e) {
        uni.hideLoading()
        if (e && e.message) {
          uni.showToast({ title: e.message, icon: 'none' })
        } else {
          console.log('update profile error:', e)
        }
      }
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.card { padding: 20px; }
.input-group { margin-bottom: 16px; }
.input-label { font-size: 14px; color: #2c2416; font-weight: 500; margin-bottom: 6px; display: block; }
.required { color: #bc4742; }
.input { width: 100%; height: 44px; border: 1px solid #c9c0ae; border-radius: 8px; padding: 0 12px; font-size: 14px; background: #fff; box-sizing: border-box; }
.submit-btn { width: 100%; text-align: center; margin-top: 20px; padding: 14px 0; font-size: 16px; border-radius: 10px; }
</style>
