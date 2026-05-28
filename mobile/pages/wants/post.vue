<template>
  <view class="container">
    <view class="card card-top-teal">
      <view class="input-group">
        <text class="input-label">书名 <text class="required">*</text></text>
        <input v-model="form.title" placeholder="请输入想要的教材名称" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">作者</text>
        <input v-model="form.author" placeholder="选填" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">出版社</text>
        <input v-model="form.publisher" placeholder="选填" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">学院</text>
        <picker :range="categories" range-key="name" @change="onCategoryChange">
          <view class="picker-view">{{ categoryName || '请选择学院（选填）' }}</view>
        </picker>
      </view>
      <view class="input-group">
        <text class="input-label">最高出价 <text class="required">*</text></text>
        <input v-model="form.max_price" type="digit" placeholder="你愿意出的最高价格" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">期望成色</text>
        <picker :range="conditionLabels" @change="onConditionChange">
          <view class="picker-view">{{ conditionLabel || '不限（选填）' }}</view>
        </picker>
      </view>
      <view class="btn-teal submit-btn" @click="onSubmit">发布求购</view>
    </view>
  </view>
</template>

<script>
import { get, post } from '@/utils/request.js'
import auth from '@/stores/auth.js'

export default {
  data() {
    return {
      categories: [],
      conditions: ['like_new', 'excellent', 'good', 'fair'],
      conditionLabels: ['全新', '几乎全新', '正常使用', '较旧'],
      categoryName: '',
      conditionLabel: '',
      form: {
        title: '',
        author: '',
        publisher: '',
        category_id: null,
        max_price: '',
        acceptable_condition: ''
      }
    }
  },
  mounted() {
    if (!auth.isLogin()) {
      uni.showToast({ title: '请先登录', icon: 'none' })
      setTimeout(function () { uni.navigateTo({ url: '/pages/auth/login' }) }, 1000)
      return
    }
    this.fetchCategories()
  },
  methods: {
    async fetchCategories() {
      try {
        const res = await get('/api/categories')
        this.categories = res.data
      } catch (e) {
        console.log('fetch categories error:', e)
      }
    },
    onCategoryChange(e) {
      const idx = e.detail.value
      this.form.category_id = this.categories[idx].id
      this.categoryName = this.categories[idx].name
    },
    onConditionChange(e) {
      this.form.acceptable_condition = this.conditions[e.detail.value]
      this.conditionLabel = this.conditionLabels[e.detail.value]
    },
    async onSubmit() {
      if (!this.form.title.trim()) { uni.showToast({ title: '请输入书名', icon: 'none' }); return }
      if (!this.form.max_price || parseFloat(this.form.max_price) <= 0) { uni.showToast({ title: '请输入有效价格', icon: 'none' }); return }

      try {
        await post('/api/wants', {
          title: this.form.title.trim(),
          author: this.form.author.trim(),
          publisher: this.form.publisher.trim(),
          category_id: this.form.category_id || null,
          max_price: parseFloat(this.form.max_price),
          acceptable_condition: this.form.acceptable_condition || null
        })
        uni.showToast({ title: '发布成功', icon: 'success' })
        setTimeout(function () {
          uni.switchTab({ url: '/pages/wants/index' })
        }, 800)
      } catch (e) {
        if (e.code === 422 && e.data && e.data.book_id) {
          uni.showModal({
            title: '提示',
            content: e.message,
            confirmText: '去看看',
            success: function (res) {
              if (res.confirm) {
                uni.navigateTo({ url: '/pages/books/detail?id=' + e.data.book_id })
              }
            }
          })
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
.input { width: 100%; height: 44px; border: 1px solid #e5dccf; border-radius: 8px; padding: 0 12px; font-size: 14px; background: #fff; box-sizing: border-box; }
.picker-view { width: 100%; height: 44px; border: 1px solid #e5dccf; border-radius: 8px; padding: 0 12px; font-size: 14px; background: #fff; line-height: 44px; color: #8c8478; box-sizing: border-box; }
.submit-btn { width: 100%; text-align: center; margin-top: 20px; padding: 14px 0; font-size: 16px; border-radius: 10px; }
</style>
