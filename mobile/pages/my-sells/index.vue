<template>
  <view class="container">
    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else>
      <view v-if="books.length === 0" class="empty-state">
        <text>暂无卖书记录</text>
        <text class="btn-amber mt-16" style="padding:8px 20px;display:inline-block;" @click="goSell">我要卖书</text>
      </view>
      <view v-for="b in books" :key="b.id" :class="['card', 'book-card', sellAccent(b.status)]" @click="goDetail(b.id)">
        <view class="book-row">
          <view v-if="b.cover_img" class="cover-wrap">
            <safe-image :src="b.cover_img" mode="aspectFill" class="cover-img" />
          </view>
          <view v-else class="cover-placeholder"></view>
          <view class="book-info">
            <text class="title ellipsis-2">{{ b.title }}</text>
            <text :class="['status-tag', statusClass(b.status)]">{{ b.status_label }}</text>
            <view class="price-row">
              <text v-if="b.price" class="price">售价 ¥{{ b.price }}</text>
              <text v-if="b.cost_price" class="cost">结算 ¥{{ b.cost_price }}</text>
            </view>
            <text v-if="b.reject_reason" class="reject-reason">驳回原因：{{ b.reject_reason }}</text>
            <text v-if="b.submitted_at" class="date">{{ b.submitted_at }}</text>
            <view class="actions-row">
              <view v-if="b.order_id" class="btn-outline btn-sm" @click.stop="goOrder(b.order_id)">查看订单</view>
              <view v-if="b.status === 'removed' || b.status === 'rejected'" class="btn-danger btn-sm" @click.stop="onDelete(b)">删除</view>
            </view>
          </view>
        </view>
      </view>
    </template>
  </view>
</template>

<script>
import { get, del } from '@/utils/request.js'
import auth from '@/stores/auth.js'

export default {
  data() {
    return {
      books: [],
      loading: true
    }
  },
  mounted: function () {
    if (!auth.isLogin()) {
      uni.showToast({ title: '请先登录', icon: 'none' })
      setTimeout(function () { uni.navigateTo({ url: '/pages/auth/login' }) }, 1000)
      return
    }
    this.fetchBooks()
  },
  methods: {
    fetchBooks: async function () {
      try {
        var res = await get('/api/my-books')
        this.books = res.data.list || []
      } catch (e) {
        console.log('fetchBooks error:', e)
      } finally {
        this.loading = false
      }
    },
    goSell: function () { uni.navigateTo({ url: '/pages/books/sell' }) },
    goDetail: function (id) { uni.navigateTo({ url: '/pages/books/detail?id=' + id }) },
    goOrder: function (id) { uni.navigateTo({ url: '/pages/orders/detail?id=' + id }) },
    onDelete: function (book) {
      var self = this
      var msg = book.status === 'rejected' ? '确定删除被驳回的书吗？' : '确定删除被下架的书吗？'
      uni.showModal({
        title: '确认删除',
        content: msg,
        success: async function (res) {
          if (!res.confirm) return
          try {
            await del('/api/my-books/' + book.id)
            uni.showToast({ title: '已删除', icon: 'success' })
            self.fetchBooks()
          } catch (e) {
            console.log('delete error:', e)
          }
        }
      })
    },
    statusClass: function (status) {
      if (status === 'active') return 'status-success'
      if (status === 'sold') return 'status-info'
      if (status === 'pending_review') return 'status-warning'
      if (status === 'removed' || status === 'rejected') return 'status-danger'
      return 'status-warning'
    },
    sellAccent: function (status) {
      if (status === 'active') return 'card-accent-green'
      if (status === 'sold') return 'card-accent-blue'
      if (status === 'pending_review') return 'card-accent-amber'
      if (status === 'removed' || status === 'rejected') return 'card-accent-red'
      return 'card-accent-gray'
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.book-card { padding: 14px; margin-bottom: 10px; }
.book-row { display: flex; }
.cover-wrap { width: 70px; height: 95px; border-radius: 6px; flex-shrink: 0; overflow: hidden; margin-right: 12px; }
.cover-img { width: 100%; height: 100%; }
.cover-placeholder { width: 70px; height: 95px; border-radius: 6px; flex-shrink: 0; background: #cec4b0; margin-right: 12px; }
.book-info { flex: 1; }
.title { font-size: 15px; font-weight: 500; color: #2c2416; line-height: 1.4; }
.status-tag { display: inline-block; font-size: 11px; padding: 1px 8px; border-radius: 10px; margin-top: 6px; }
.status-success { background: #f0fdfa; color: #0d9488; }
.status-info { background: rgba(180,148,80,0.08); color: #b49450; }
.status-warning { background: #fefce8; color: #b0822c; }
.status-danger { background: #fef2f2; color: #bc4742; }
.price-row { display: flex; margin-top: 6px; }
.price { margin-right: 12px; }
.price { font-size: 14px; color: #b49450; font-weight: 600; }
.cost { font-size: 12px; color: #6e6559; }
.reject-reason { font-size: 12px; color: #bc4742; margin-top: 4px; display: block; }
.date { font-size: 11px; color: #6e6559; margin-top: 4px; display: block; }
.actions-row { display: flex; margin-top: 8px; }
.btn-sm { padding: 4px 12px; font-size: 12px; border-radius: 6px; margin-right: 8px; }
.status-msg { text-align: center; padding: 100px 0; color: #6e6559; }
</style>
