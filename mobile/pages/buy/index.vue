<template>
  <view class="container">
    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else-if="book">
      <view class="card card-accent-amber">
        <view class="book-row">
          <safe-image v-if="book.cover_img" :src="book.cover_img" mode="aspectFill" class="cover" />
          <view class="book-info">
            <text class="title ellipsis-2">{{ book.title }}</text>
            <text class="meta">{{ book.author }}</text>
            <text class="price">¥{{ book.price }}</text>
          </view>
        </view>
      </view>

      <view class="card">
        <text class="section-title">取书地点</text>
        <input v-model="pickupLocation" class="input" placeholder="请填写取书地点，如图书馆门口" />
      </view>

      <view class="card">
        <text class="section-title">下单须知</text>
        <view class="notice">
          <text class="notice-item">1. 下单后请在24小时内完成支付，超时订单将自动取消</text>
          <text class="notice-item">2. 付款后平台客服会联系你确认取书时间和地点</text>
          <text class="notice-item">3. 取书时请当场验书，确认无误后再确认取书</text>
          <text class="notice-item">4. 确认取书后订单完结，不再支持退款</text>
        </view>
      </view>

      <view class="btn-amber submit-btn" @click="onSubmit">确认下单 ¥{{ book.price }}</view>
    </template>
  </view>
</template>

<script>
import { get, post } from '@/utils/request.js'

export default {
  data() {
    return {
      book: null,
      pickupLocation: '',
      loading: true
    }
  },
  mounted() {
    var pages = getCurrentPages()
    var bookId = pages[pages.length - 1].options.bookId
    if (bookId) this.fetchBook(bookId)
  },
  methods: {
    async fetchBook(id) {
      try {
        var res = await get('/api/books/' + id)
        this.book = res.data
      } catch (e) {
        console.log('fetchBook error:', e)
      } finally {
        this.loading = false
      }
    },
    async onSubmit() {
      if (!this.pickupLocation.trim()) {
        uni.showToast({ title: '请填写取书地点', icon: 'none' })
        return
      }
      try {
        var res = await post('/api/orders', {
          book_ids: [this.book.id],
          pickup_location: this.pickupLocation.trim()
        })
        uni.showToast({ title: '下单成功', icon: 'success' })
        setTimeout(function () {
          uni.redirectTo({ url: '/pages/orders/detail?id=' + res.data.order_id })
        }, 1000)
      } catch (e) {
        console.log('submit order error:', e)
      }
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.card { margin-bottom: 12px; padding: 16px; }
.book-row { display: flex; }
.cover { width: 80px; height: 110px; border-radius: 8px; flex-shrink: 0; background: #e5dccf; margin-right: 12px; }
.book-info { flex: 1; display: flex; flex-direction: column; }
.book-info .title { font-size: 15px; font-weight: 600; color: #2c2416; line-height: 1.4; }
.book-info .meta { font-size: 13px; color: #8c8478; margin-top: 4px; }
.book-info .price { font-size: 18px; margin-top: 8px; }
.section-title { font-size: 16px; font-weight: 600; color: #2c2416; margin-bottom: 12px; display: block; }
.input { width: 100%; height: 44px; border: 1px solid #e5dccf; border-radius: 8px; padding: 0 12px; font-size: 14px; background: #fff; box-sizing: border-box; }
.notice-item { font-size: 13px; color: #8c8478; line-height: 1.8; display: block; }
.submit-btn { width: 100%; text-align: center; margin-top: 20px; padding: 14px 0; font-size: 16px; border-radius: 10px; }
.status-msg { text-align: center; padding: 100px 0; color: #8c8478; }
</style>
