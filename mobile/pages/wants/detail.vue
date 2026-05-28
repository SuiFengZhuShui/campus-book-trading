<template>
  <view class="container">
    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else-if="want">
      <view class="card">
        <text class="title">{{ want.title }}</text>
        <view class="meta">
          <text v-if="want.author" class="meta-item">{{ want.author }}</text>
          <text v-if="want.publisher" class="meta-item">{{ want.publisher }}</text>
          <text v-if="want.category_name" class="meta-item">{{ want.category_name }}</text>
        </view>
        <view class="info-grid">
          <view class="info-item">
            <text class="label">最高出价</text>
            <text class="price">¥{{ want.max_price }}</text>
          </view>
          <view class="info-item">
            <text class="label">期望成色</text>
            <text class="value">{{ want.condition_label }}</text>
          </view>
          <view class="info-item">
            <text class="label">发布者</text>
            <text class="value">{{ want.publisher_name }}</text>
          </view>
          <view class="info-item">
            <text class="label">过期时间</text>
            <text class="value">{{ want.expires_at }}</text>
          </view>
        </view>
        <text :class="['status-tag', wantStatusClass(want.status)]">{{ want.status_label }}</text>
      </view>

      <view v-if="want.fulfillments && want.fulfillments.length" class="card">
        <text class="section-title">接单记录</text>
        <view v-for="f in want.fulfillments" :key="f.id" class="fulfill-item">
          <text class="fulfiller">{{ f.fulfiller_name }}</text>
          <text class="fulfill-status">{{ f.status_label }}</text>
        </view>
      </view>

      <view v-if="want.status === 'active'" class="btn-teal sell-btn" @click="goSell">
        我要卖这本书
      </view>
    </template>
  </view>
</template>

<script>
import { get } from '@/utils/request.js'

export default {
  data() {
    return {
      want: null,
      loading: true
    }
  },
  mounted() {
    const pages = getCurrentPages()
    const id = pages[pages.length - 1].options.id
    if (id) this.fetchWant(id)
  },
  methods: {
    async fetchWant(id) {
      try {
        const res = await get('/api/wants/' + id)
        this.want = res.data
      } catch (e) {
        console.log('fetchWant error:', e)
      } finally {
        this.loading = false
      }
    },
    goSell() {
      const w = this.want
      const params = 'title=' + encodeURIComponent(w.title) +
        (w.author ? '&author=' + encodeURIComponent(w.author) : '') +
        (w.publisher ? '&publisher=' + encodeURIComponent(w.publisher) : '') +
        (w.category_id ? '&category_id=' + w.category_id : '')
      uni.navigateTo({ url: '/pages/books/sell?' + params })
    },
    wantStatusClass(status) {
      if (status === 'active') return 'status-success'
      if (status === 'fulfilled') return 'status-info'
      return 'status-muted'
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.card { padding: 16px; margin-bottom: 10px; }
.title { font-size: 20px; font-weight: 700; color: #2c2416; display: block; line-height: 1.4; }
.meta { display: flex; flex-wrap: wrap; margin-top: 10px; }
.meta-item { font-size: 12px; color: #8c8478; background: rgba(180,148,80,0.06); padding: 1px 8px; border-radius: 4px; margin-right: 6px; margin-bottom: 4px; }
.info-grid { display: flex; flex-wrap: wrap; margin-top: 16px; }
.info-item { width: 50%; margin-bottom: 12px; }
.label { font-size: 12px; color: #8c8478; display: block; }
.info-item .price { font-size: 18px; }
.value { font-size: 14px; color: #2c2416; }
.status-tag { display: inline-block; font-size: 11px; padding: 2px 10px; border-radius: 10px; margin-top: 12px; }
.status-success { background: #f0fdfa; color: #0d9488; }
.status-info { background: rgba(180,148,80,0.08); color: #b49450; }
.status-muted { background: rgba(180,148,80,0.06); color: #8c8478; }
.section-title { font-size: 16px; font-weight: 600; color: #2c2416; margin-bottom: 12px; display: block; }
.fulfill-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5dccf; }
.fulfill-item:last-child { border-bottom: none; }
.fulfiller { font-size: 14px; color: #2c2416; }
.fulfill-status { font-size: 12px; color: #8c8478; }
.sell-btn { width: 100%; text-align: center; padding: 14px 0; font-size: 16px; border-radius: 10px; margin-top: 20px; }
.status-msg { text-align: center; padding: 100px 0; color: #8c8478; }
</style>
