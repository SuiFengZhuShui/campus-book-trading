<template>
  <view class="container">
    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else-if="order">
      <view class="card">
        <view class="order-header">
          <text class="order-no">{{ order.order_no }}</text>
          <text :class="['status-tag', statusClass(order.status)]">{{ order.status_label }}</text>
        </view>
        <view class="books-section">
          <view v-for="b in order.books" :key="b.book_id" class="book-row">
            <text class="book-title">{{ b.title }}</text>
            <text class="book-price">¥{{ b.price }}</text>
          </view>
        </view>
        <view class="info-row">
          <text class="label">取书地点</text>
          <text class="value">{{ order.pickup_location }}</text>
        </view>
        <view class="divider"></view>
        <view class="info-row">
          <text class="label">合计金额</text>
          <text class="amount">¥{{ order.total_amount }}</text>
        </view>
        <view class="info-row mt-12">
          <text class="label">下单时间</text>
          <text class="value">{{ order.created_at }}</text>
        </view>
      </view>

      <view v-if="order.seller" class="card">
        <text class="section-title">卖家信息</text>
        <view class="info-row">
          <text class="label">姓名</text>
          <text class="value">{{ order.seller.name }}</text>
        </view>
        <view class="info-row mt-12">
          <text class="label">手机号</text>
          <text class="value">{{ order.seller.phone }}</text>
        </view>
      </view>

      <view v-if="order.timeline && order.timeline.length" class="card">
        <text class="section-title">订单轨迹</text>
        <view v-for="(t, idx) in order.timeline" :key="idx" class="timeline-item">
          <view :class="['dot', idx === 0 ? 'dot-active' : '']"></view>
          <view class="timeline-content">
            <text class="timeline-status">{{ t.remark || timelineLabel(t.status) }}</text>
            <text class="timeline-date">{{ t.created_at }}</text>
          </view>
        </view>
      </view>

      <view class="actions">
        <view v-if="order.status === 'pending'" class="btn-danger action-btn" @click="onCancel">取消订单</view>
        <view v-if="order.status === 'pending'" class="btn-amber action-btn" @click="onPay">立即支付</view>
        <view v-if="order.status === 'paid'" class="btn-amber action-btn" @click="onPickup">确认取书</view>
        <view v-if="order.status === 'picked_up'" class="btn-primary action-btn" @click="goReview">去评价</view>
      </view>
    </template>
  </view>
</template>

<script>
import { get, post } from '@/utils/request.js'

export default {
  data() {
    return {
      order: null,
      loading: true
    }
  },
  mounted: function () {
    var pages = getCurrentPages()
    var id = pages[pages.length - 1].options.id
    if (id) this.fetchOrder(id)
  },
  methods: {
    fetchOrder: async function (id) {
      try {
        var res = await get('/api/orders/' + id)
        this.order = res.data
      } catch (e) {
        console.log('fetchOrder error:', e)
      } finally {
        this.loading = false
      }
    },
    onPay: async function () {
      try {
        await post('/api/orders/' + this.order.id + '/pay')
        uni.showToast({ title: '支付成功', icon: 'success' })
        var self = this
        setTimeout(function () { self.fetchOrder(self.order.id) }, 800)
      } catch (e) {
        console.log('pay error:', e)
      }
    },
    onCancel: function () {
      var self = this
      uni.showModal({
        title: '取消订单',
        content: '确定要取消此订单吗？',
        success: async function (res) {
          if (!res.confirm) return
          try {
            await post('/api/orders/' + self.order.id + '/cancel', { reason: '用户主动取消' })
            uni.showToast({ title: '已取消', icon: 'success' })
            setTimeout(function () { self.fetchOrder(self.order.id) }, 800)
          } catch (e) {
            console.log('cancel error:', e)
          }
        }
      })
    },
    onPickup: function () {
      var self = this
      uni.showModal({
        title: '确认取书',
        content: '确认已收到书籍？确认后订单完结。',
        success: async function (res) {
          if (!res.confirm) return
          try {
            await post('/api/orders/' + self.order.id + '/pickup')
            uni.showToast({ title: '确认成功', icon: 'success' })
            setTimeout(function () { self.fetchOrder(self.order.id) }, 800)
          } catch (e) {
            console.log('pickup error:', e)
          }
        }
      })
    },
    goReview: function () {
      uni.navigateTo({ url: '/pages/orders/review?id=' + this.order.id + '&bookId=' + (this.order.books[0] ? this.order.books[0].book_id : '') })
    },
    statusClass: function (status) {
      if (status === 'pending') return 'status-warning'
      if (status === 'paid' || status === 'confirmed') return 'status-info'
      if (status === 'picked_up') return 'status-success'
      if (status === 'cancelled') return 'status-danger'
      return ''
    },
    timelineLabel: function (status) {
      var map = { pending: '下单', paid: '支付成功', confirmed: '平台确认', picked_up: '取书完成', cancelled: '已取消' }
      return map[status] || status
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.card { padding: 16px; margin-bottom: 10px; }
.order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
.order-no { font-size: 13px; color: #8c8478; }
.status-tag { font-size: 12px; padding: 2px 8px; border-radius: 12px; }
.status-warning { background: #fefce8; color: #b0822c; }
.status-info { background: rgba(180,148,80,0.08); color: #b49450; }
.status-success { background: #f0fdf4; color: #2d6a4f; }
.status-danger { background: #fef2f2; color: #bc4742; }
.book-row { display: flex; justify-content: space-between; padding: 6px 0; }
.book-title { font-size: 14px; color: #2c2416; flex: 1; }
.book-price { font-size: 14px; color: #b49450; font-weight: 600; }
.info-row { display: flex; justify-content: space-between; align-items: center; }
.info-row .label { font-size: 13px; color: #8c8478; }
.info-row .value { font-size: 13px; color: #2c2416; }
.amount { font-size: 18px; color: #b49450; font-weight: 700; }
.section-title { font-size: 16px; font-weight: 600; color: #2c2416; margin-bottom: 12px; display: block; padding-left: 10px; border-left: 3px solid #b49450; }
.timeline-item { display: flex; padding-left: 6px; margin-bottom: 12px; }
.timeline-item:last-child { margin-bottom: 0; }
.dot { width: 10px; height: 10px; border-radius: 50%; background: #e5dccf; margin-top: 4px; flex-shrink: 0; margin-right: 10px; }
.dot-active { background: linear-gradient(135deg, #b49450, #d4bc7c); }
.timeline-content { flex: 1; }
.timeline-status { font-size: 13px; color: #2c2416; display: block; }
.timeline-date { font-size: 11px; color: #8c8478; }
.actions { display: flex; margin-top: 20px; }
.action-btn { margin-right: 10px; }
.action-btn { flex: 1; text-align: center; padding: 12px 0; border-radius: 8px; font-size: 15px; }
.status-msg { text-align: center; padding: 100px 0; color: #8c8478; }
</style>
