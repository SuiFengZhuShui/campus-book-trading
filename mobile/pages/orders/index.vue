<template>
  <view class="container">
    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else>
      <view v-if="orders.length === 0" class="empty-state">
        <text>暂无订单</text>
        <text class="mt-12" style="font-size:12px;color:#8c8478;">去首页逛逛吧</text>
      </view>
      <view v-for="o in orders" :key="o.id" :class="['card', 'order-card', orderAccent(o.status)]" @click="goDetail(o.id)">
        <view class="order-header">
          <text class="order-no">{{ o.order_no }}</text>
          <text :class="['status-tag', statusClass(o.status)]">{{ o.status_label }}</text>
        </view>
        <view class="order-body">
          <image v-if="o.cover_img" :src="o.cover_img" mode="aspectFill" class="cover" />
          <view class="order-info">
            <text class="book-title ellipsis-2">{{ o.books[0] ? o.books[0].title : '' }}</text>
            <text v-if="o.books.length > 1" class="book-count">共{{ o.books.length }}本书</text>
          </view>
        </view>
        <view class="order-footer">
          <text class="amount">合计 ¥{{ o.total_amount }}</text>
          <text class="date">{{ o.created_at }}</text>
        </view>
      </view>
    </template>
  </view>
</template>

<script>
import { get } from '@/utils/request.js'
import auth from '@/stores/auth.js'

export default {
  data() {
    return {
      orders: [],
      loading: true,
      filterStatus: ''
    }
  },
  onLoad: function (options) {
    if (options && options.status) {
      this.filterStatus = options.status
    }
  },
  mounted: function () {
    if (!auth.isLogin()) {
      uni.showToast({ title: '请先登录', icon: 'none' })
      setTimeout(function () { uni.navigateTo({ url: '/pages/auth/login' }) }, 1000)
      return
    }
    this.fetchOrders()
  },
  methods: {
    fetchOrders: async function () {
      try {
        var params = {}
        if (this.filterStatus) {
          params.status = this.filterStatus
        }
        var res = await get('/api/orders', params)
        this.orders = res.data.list || []
      } catch (e) {
        console.log('fetchOrders error:', e)
      } finally {
        this.loading = false
      }
    },
    goDetail: function (id) { uni.navigateTo({ url: '/pages/orders/detail?id=' + id }) },
    statusClass: function (status) {
      if (status === 'pending') return 'status-warning'
      if (status === 'paid' || status === 'confirmed') return 'status-info'
      if (status === 'picked_up') return 'status-success'
      if (status === 'cancelled') return 'status-danger'
      return ''
    },
    orderAccent: function (status) {
      if (status === 'pending') return 'card-accent-amber'
      if (status === 'paid') return 'card-accent-blue'
      if (status === 'confirmed') return 'card-accent-indigo'
      if (status === 'picked_up') return 'card-accent-green'
      if (status === 'cancelled') return 'card-accent-gray'
      return ''
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.order-card { padding: 16px; margin-bottom: 10px; }
.order-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.order-no { font-size: 13px; color: #8c8478; }
.status-tag { font-size: 12px; padding: 2px 8px; border-radius: 12px; }
.status-warning { background: #fefce8; color: #b0822c; }
.status-info { background: rgba(180,148,80,0.08); color: #b49450; }
.status-success { background: #f0fdf4; color: #2d6a4f; }
.status-danger { background: #fef2f2; color: #bc4742; }
.order-body { display: flex; }
.cover { margin-right: 10px; width: 60px; height: 80px; border-radius: 6px; flex-shrink: 0; background: #e5dccf; }
.order-info { flex: 1; }
.book-title { font-size: 14px; color: #2c2416; line-height: 1.4; }
.book-count { font-size: 12px; color: #8c8478; }
.order-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 10px; border-top: 1px solid #e5dccf; }
.amount { font-size: 15px; font-weight: 600; color: #b49450; }
.date { font-size: 12px; color: #8c8478; }
.status-msg { text-align: center; padding: 100px 0; color: #8c8478; }
</style>
