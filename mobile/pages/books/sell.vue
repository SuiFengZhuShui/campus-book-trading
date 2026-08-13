<template>
  <view class="container">
    <view class="card form-card card-top-amber">
      <view class="input-group">
        <text class="input-label">书名 <text class="required">*</text></text>
        <input v-model="form.title" placeholder="请输入教材名称" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">作者 <text class="required">*</text></text>
        <input v-model="form.author" placeholder="请输入作者" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">出版社 <text class="required">*</text></text>
        <input v-model="form.publisher" placeholder="请输入出版社" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">ISBN</text>
        <input v-model="form.isbn" placeholder="选填" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">学院 <text class="required">*</text></text>
        <picker :range="categories" range-key="name" @change="onCategoryChange">
          <view class="picker-view">{{ categoryName || '请选择学院' }}</view>
        </picker>
      </view>
      <view class="input-group">
        <text class="input-label">成色 <text class="required">*</text></text>
        <picker :range="conditionLabels" @change="onConditionChange">
          <view class="picker-view">{{ conditionLabel || '请选择成色' }}</view>
        </picker>
      </view>
      <view class="input-group">
        <text class="input-label">定价 <text class="required">*</text></text>
        <input v-model="form.original_price" type="digit" placeholder="请输入教材原价" class="input" />
      </view>
      <view class="input-group">
        <text class="input-label">补充说明</text>
        <textarea v-model="form.description" placeholder="选填，最多500字" maxlength="500" class="textarea" />
      </view>

      <view class="input-group">
        <text class="input-label">上传图片（2-5张，需含封面）</text>
        <view class="image-upload">
          <view v-for="(img, idx) in images" :key="idx" class="image-item" @click="removeImage(idx)">
            <safe-image :src="img" mode="aspectFill" class="upload-img" />
            <text class="remove-icon">×</text>
          </view>
          <view v-if="images.length < 5" class="add-btn" @click="chooseImage">
            <text class="add-icon">+</text>
            <text class="add-text">{{ images.length ? '' + images.length + '/5' : '添加图片' }}</text>
          </view>
        </view>
        <text v-if="images.length < 2" class="hint">请至少上传2张图片（含封面+内页）</text>
      </view>

      <view class="btn-amber submit-btn" @click="onSubmit">提交审核</view>

      <view v-if="successMsg" class="success-msg">{{ successMsg }}</view>
    </view>
  </view>
</template>

<script>
import { get, uploadFiles } from '@/utils/request.js'
import auth from '@/stores/auth.js'

export default {
  data() {
    return {
      categories: [],
      conditions: ['like_new', 'excellent', 'good', 'fair'],
      conditionLabels: ['全新', '几乎全新', '正常使用', '较旧'],
      images: [],
      categoryName: '',
      conditionLabel: '',
      successMsg: '',
      wantId: null,
      form: {
        title: '',
        author: '',
        publisher: '',
        isbn: '',
        category_id: null,
        condition: '',
        original_price: '',
        description: ''
      }
    }
  },
  mounted: async function () {
    if (!auth.isLogin()) {
      uni.redirectTo({ url: '/pages/auth/login' })
      return
    }
    var self = this
    try {
      var res = await get('/api/categories')
      self.categories = res.data
    } catch (e) {
      console.log('fetch categories error:', e)
    }
    // 预填（从求购跳转）
    var pages = getCurrentPages()
    var opts = pages[pages.length - 1].options || {}
    if (opts.title) self.form.title = opts.title
    if (opts.author) self.form.author = opts.author
    if (opts.publisher) self.form.publisher = opts.publisher
    if (opts.category_id && self.categories.length) {
      self.form.category_id = parseInt(opts.category_id)
      var c = self.categories.find(function (c) { return c.id === self.form.category_id })
      if (c) self.categoryName = c.name
    }
    if (opts.want_id) self.wantId = parseInt(opts.want_id)
  },
  methods: {
    onCategoryChange: function (e) {
      var idx = e.detail.value
      this.form.category_id = this.categories[idx].id
      this.categoryName = this.categories[idx].name
    },
    onConditionChange: function (e) {
      this.form.condition = this.conditions[e.detail.value]
      this.conditionLabel = this.conditionLabels[e.detail.value]
    },
    chooseImage: function () {
      var self = this
      uni.chooseImage({
        count: 5 - self.images.length,
        sizeType: ['compressed'],
        success: function (res) {
          self.images = self.images.concat(res.tempFilePaths)
        }
      })
    },
    removeImage: function (idx) {
      this.images.splice(idx, 1)
    },
    onSubmit: async function () {
      if (!this.form.title.trim()) { uni.showToast({ title: '请输入书名', icon: 'none' }); return }
      if (!this.form.author.trim()) { uni.showToast({ title: '请输入作者', icon: 'none' }); return }
      if (!this.form.publisher.trim()) { uni.showToast({ title: '请输入出版社', icon: 'none' }); return }
      if (!this.form.category_id) { uni.showToast({ title: '请选择学院', icon: 'none' }); return }
      if (!this.form.condition) { uni.showToast({ title: '请选择成色', icon: 'none' }); return }
      if (!this.form.original_price || parseFloat(this.form.original_price) <= 0) { uni.showToast({ title: '请输入有效定价', icon: 'none' }); return }
      if (this.images.length < 2) { uni.showToast({ title: '请上传至少2张图片', icon: 'none' }); return }

      if (this.wantId) {
        try {
          var wantCheck = await get('/api/wants/' + this.wantId)
          if (wantCheck.data && wantCheck.data.status !== 'active') {
            uni.showToast({ title: '该求购已被其他人响应', icon: 'none' })
            return
          }
        } catch (e) { console.log('check want error:', e) }
      }

      uni.showLoading({ title: '提交中...' })
      try {
        var fields = {
          title: this.form.title.trim(),
          author: this.form.author.trim(),
          publisher: this.form.publisher.trim(),
          isbn: this.form.isbn.trim(),
          category_id: this.form.category_id,
          condition: this.form.condition,
          original_price: parseFloat(this.form.original_price),
          description: this.form.description.trim()
        }
        if (this.wantId) fields.want_id = this.wantId

        var imageTypes = ['cover']
        for (var i = 1; i < this.images.length; i++) {
          imageTypes.push('other')
        }

        // uploadFiles sends images[] as multipart/form-data (H5: native fetch, others: uni.uploadFile)
        await uploadFiles('/api/books/submit', this.images, fields, imageTypes)

        uni.hideLoading()
        this.successMsg = '提交成功，等待管理员审核定价后上架'
        this.form = { title: '', author: '', publisher: '', isbn: '', category_id: null, condition: '', original_price: '', description: '' }
        this.images = []
        this.categoryName = ''
        this.conditionLabel = ''
        this.wantId = null
      } catch (e) {
        uni.hideLoading()
        uni.showToast({ title: e.message || '提交失败', icon: 'none' })
        console.log('submit book error:', e)
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
.textarea { width: 100%; height: 80px; border: 1px solid #e5dccf; border-radius: 8px; padding: 10px 12px; font-size: 14px; background: #fff; box-sizing: border-box; }
.picker-view { width: 100%; height: 44px; border: 1px solid #e5dccf; border-radius: 8px; padding: 0 12px; font-size: 14px; background: #fff; line-height: 44px; color: #8c8478; box-sizing: border-box; }
.image-upload { display: flex; flex-wrap: wrap; }
.image-item { position: relative; width: 80px; height: 80px; border-radius: 8px; overflow: hidden; margin-right: 8px; margin-bottom: 8px; }
.upload-img { width: 100%; height: 100%; }
.remove-icon { position: absolute; top: 2px; right: 2px; width: 20px; height: 20px; background: rgba(0,0,0,0.5); color: #fff; border-radius: 50%; text-align: center; line-height: 18px; font-size: 14px; }
.add-btn { width: 80px; height: 80px; border: 2px dashed #e5dccf; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #fff; }
.add-icon { font-size: 28px; color: #b49450; line-height: 1; }
.add-text { font-size: 11px; color: #8c8478; margin-top: 2px; }
.hint { font-size: 12px; color: #bc4742; margin-top: 6px; display: block; }
.submit-btn { width: 100%; text-align: center; margin-top: 20px; padding: 14px 0; font-size: 16px; border-radius: 10px; }
.success-msg { background: rgba(74,103,65,0.12); color: #2d6a4f; border: 1px solid rgba(74,103,65,0.25); padding: 12px 16px; border-radius: 8px; margin-top: 12px; font-size: 14px; font-weight: 500; text-align: center; }
</style>
