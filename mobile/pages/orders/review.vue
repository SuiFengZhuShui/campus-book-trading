<template>
  <view class="container">
    <view class="card">
      <text class="section-title">评价</text>
      <view class="rating-row">
        <text class="rating-label">书籍评分</text>
        <view class="stars">
          <text v-for="i in 5" :key="i" :class="i <= bookRating ? 'star on' : 'star'" @click="bookRating = i">★</text>
        </view>
      </view>
      <view class="rating-row mt-12">
        <text class="rating-label">服务评分</text>
        <view class="stars">
          <text v-for="i in 5" :key="i" :class="i <= serviceRating ? 'star on' : 'star'" @click="serviceRating = i">★</text>
        </view>
      </view>
      <view class="mt-16">
        <text class="rating-label">评价内容（选填）</text>
        <textarea v-model="comment" placeholder="分享你的体验..." maxlength="500" class="textarea" />
      </view>
      <view class="btn-amber submit-btn" @click="onSubmit">提交评价</view>
    </view>
  </view>
</template>

<script>
import { post } from '@/utils/request.js'

export default {
  data() {
    return {
      orderId: '',
      bookRating: 5,
      serviceRating: 5,
      comment: ''
    }
  },
  mounted: function () {
    var pages = getCurrentPages()
    var opts = pages[pages.length - 1].options
    this.orderId = opts.id
  },
  methods: {
    onSubmit: async function () {
      try {
        await post('/api/orders/' + this.orderId + '/review', {
          book_rating: this.bookRating,
          service_rating: this.serviceRating,
          comment: this.comment.trim()
        })
        uni.showToast({ title: '评价成功', icon: 'success' })
        setTimeout(function () { uni.navigateBack() }, 800)
      } catch (e) {
        console.log('review error:', e)
      }
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; }
.card { padding: 20px; }
.section-title { font-size: 18px; font-weight: 600; color: #2c2416; display: block; margin-bottom: 16px; padding-left: 10px; border-left: 3px solid #b49450; }
.rating-row { display: flex; align-items: center; }
.rating-label { font-size: 14px; color: #8c8478; margin-right: 12px; }
.stars { display: flex; }
.star { font-size: 28px; color: #e5dccf; margin-right: 4px; }
.star.on { background: linear-gradient(135deg, #f59e0b, #b49450); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.textarea { width: 100%; height: 100px; border: 1px solid #e5dccf; border-radius: 8px; padding: 10px; font-size: 14px; background: #fff; box-sizing: border-box; }
.submit-btn { width: 100%; text-align: center; padding: 14px 0; font-size: 16px; border-radius: 10px; margin-top: 20px; }
</style>
