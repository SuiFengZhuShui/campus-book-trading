<template>
  <view class="container">
    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else-if="book">
      <swiper v-if="book.images.length" class="image-swiper" :indicator-dots="true" indicator-color="#e5dccf" indicator-active-color="#b49450" circular>
        <swiper-item v-for="img in book.images" :key="img.id">
          <safe-image :src="img.url" mode="aspectFill" class="swiper-image" @click="previewImage(img.url)" />
        </swiper-item>
      </swiper>
      <view v-else class="no-cover">暂无图片</view>

      <view class="card info-card">
        <text class="title">{{ book.title }}</text>
        <view class="meta">
          <text class="meta-item">{{ book.author }}</text>
          <text class="meta-item">{{ book.publisher }}</text>
          <text v-if="book.isbn" class="meta-item">ISBN {{ book.isbn }}</text>
        </view>
        <view class="tags">
          <text class="condition-tag">{{ book.condition_label }}</text>
          <text v-if="book.category" class="category-tag">{{ book.category }}</text>
        </view>
        <view class="price-row">
          <text v-if="book.price" class="price">¥{{ book.price }}</text>
          <text v-else class="price-pending">审核中，待定价</text>
          <text v-if="book.original_price" class="price-original">¥{{ book.original_price }}</text>
        </view>
        <text v-if="book.description" class="desc">{{ book.description }}</text>
      </view>

      <view v-if="book.seller" class="card seller-card">
        <text class="section-title">卖家信息</text>
        <view class="seller-row">
          <text class="seller-name">{{ book.seller.name }}</text>
          <text class="seller-phone">{{ book.seller.phone }}</text>
        </view>
      </view>

      <view v-if="book.reviews.length" class="card reviews-card">
        <text class="section-title">评价 ({{ book.reviews.length }})</text>
        <view v-for="r in book.reviews" :key="r.id" class="review-item">
          <view class="review-header">
            <text class="review-user">{{ r.user_name }}</text>
            <view class="review-stars">
              <text v-for="i in 5" :key="i" :class="i <= r.book_rating ? 'star on' : 'star'">★</text>
            </view>
          </view>
          <text v-if="r.comment" class="review-comment">{{ r.comment }}</text>
          <text class="review-date">{{ r.created_at }}</text>
        </view>
      </view>

      <view v-if="book.status === 'active'" class="bottom-bar">
        <view class="bar-info">
          <text class="price">¥{{ book.price }}</text>
          <text class="condition">{{ book.condition_label }}</text>
        </view>
        <view class="btn-amber bar-btn" @click="goBuy">立即购买</view>
      </view>
    </template>
  </view>
</template>

<script>
import { get } from '@/utils/request.js'

export default {
  data() {
    return {
      book: null,
      loading: true
    }
  },
  mounted() {
    var pages = getCurrentPages()
    var id = pages[pages.length - 1].options.id
    if (id) this.fetchBook(id)
  },
  methods: {
    async fetchBook(id) {
      this.loading = true
      try {
        var res = await get('/api/books/' + id)
        this.book = res.data
      } catch (e) {
        console.log('fetchBook error:', e)
        uni.showToast({ title: '加载失败', icon: 'none' })
      } finally {
        this.loading = false
      }
    },
    previewImage(url) {
      var urls = this.book.images.map(function (img) { return img.url })
      uni.previewImage({ current: url, urls: urls })
    },
    goBuy() {
      uni.navigateTo({ url: '/pages/buy/index?bookId=' + this.book.id })
    }
  }
}
</script>

<style scoped>
.container { min-height: 100vh; background: #fdfaf4; }
.image-swiper { width: 100%; height: 400px; }
.swiper-image { width: 100%; height: 100%; }
.no-cover { width: 100%; height: 240px; display: flex; align-items: center; justify-content: center; background: #e5dccf; color: #8c8478; font-size: 14px; }
.card { margin: 12px; padding: 16px; }
.info-card .title { font-size: 20px; font-weight: 700; color: #2c2416; line-height: 1.4; display: block; }
.meta { margin-top: 10px; display: flex; flex-wrap: wrap; }
.meta-item { font-size: 13px; color: #8c8478; background: rgba(180,148,80,0.06); padding: 2px 8px; border-radius: 4px; margin-right: 6px; margin-bottom: 6px; }
.tags { display: flex; margin-top: 12px; }
.condition-tag { font-size: 12px; color: #2d6a4f; background: #f0fdf4; padding: 2px 10px; border-radius: 12px; margin-right: 8px; }
.category-tag { font-size: 12px; color: #5b7fbd; background: #f0f4ff; padding: 2px 10px; border-radius: 12px; }
.price-row { display: flex; align-items: baseline; margin-top: 14px; background: linear-gradient(135deg, #fffdfa 0%, #fef9f0 100%); padding: 12px; border-radius: 10px; border: 1px solid #e5dccf; }
.price-row .price { font-size: 24px; margin-right: 8px; }
.price-pending { font-size: 16px; color: #b0822c; font-weight: 500; }
.price-pending-sm { font-size: 13px; color: #b0822c; font-weight: 500; }
.desc { font-size: 14px; color: #8c8478; line-height: 1.6; margin-top: 12px; display: block; }
.section-title { font-size: 16px; font-weight: 600; color: #2c2416; margin-bottom: 12px; display: block; padding-left: 10px; border-left: 3px solid #b49450; }
.seller-card { border-left: 3px solid #b49450; }
.seller-card .seller-row { display: flex; }
.seller-name { font-size: 14px; color: #2c2416; margin-right: 16px; }
.seller-phone { font-size: 14px; color: #8c8478; }
.review-item { padding: 12px 0; border-bottom: 1px solid #e5dccf; }
.review-item:last-child { border-bottom: none; }
.review-header { display: flex; justify-content: space-between; align-items: center; }
.review-user { font-size: 14px; color: #2c2416; font-weight: 500; }
.star { color: #e5dccf; font-size: 14px; }
.star.on { background: linear-gradient(135deg, #f59e0b, #b49450); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.review-comment { font-size: 13px; color: #8c8478; line-height: 1.5; margin-top: 6px; display: block; }
.review-date { font-size: 11px; color: #8c8478; margin-top: 4px; display: block; }
.bottom-bar { position: fixed; bottom: 0; left: 0; right: 0; display: flex; align-items: center; background: #fffdfa; padding: 10px 16px; border-top: 1px solid #e5dccf; padding-bottom: 20px; }
.bar-info { flex: 1; display: flex; flex-direction: column; }
.bar-info .price { font-size: 20px; }
.bar-info .condition { font-size: 11px; color: #8c8478; }
.bar-btn { padding: 12px 32px; font-size: 15px; border-radius: 10px; }
.status-msg { text-align: center; padding: 100px 0; color: #8c8478; }
</style>
