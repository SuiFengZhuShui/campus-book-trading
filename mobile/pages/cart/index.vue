<template>
  <view class="container">
    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else>
      <view v-if="items.length === 0" class="empty-state">
        <text class="empty-icon">🛒</text>
        <text>购物车是空的</text>
        <view class="btn-amber mt-16" style="padding:8px 24px;display:inline-block;" @click="goHome">去逛逛</view>
      </view>
      <template v-else>
        <view v-for="item in items" :key="item.id" class="card cart-item">
          <label class="item-row">
            <checkbox :checked="selectedIds.indexOf(item.book_id) !== -1" @click="toggleSelect(item.book_id)" style="transform:scale(0.8);flex-shrink:0;" />
            <view v-if="item.cover_img" class="cover-wrap">
              <safe-image :src="item.cover_img" mode="aspectFill" class="cover-img" />
            </view>
            <view v-else class="cover-placeholder"></view>
            <view class="item-info">
              <text class="title ellipsis-2">{{ item.title }}</text>
              <text class="author">{{ item.author }}</text>
              <text class="price">¥{{ item.price }}</text>
            </view>
            <view class="btn-remove" @click.stop="onRemove(item.id)">删除</view>
          </label>
        </view>

        <view class="bottom-bar">
          <label class="select-all" @click="toggleAll">
            <checkbox :checked="allSelected" style="transform:scale(0.8);" />
            <text>全选</text>
          </label>
          <view class="total-row">
            <text class="total-label">合计：</text>
            <text class="total-amount">¥{{ totalAmount.toFixed(2) }}</text>
          </view>
          <view class="btn-group">
            <view class="btn-clear" @click="onClear">清空</view>
            <view class="btn-amber checkout-btn" @click="onCheckout">去结算</view>
          </view>
        </view>

        <view class="card pickup-card">
          <text class="label">取书地点</text>
          <picker :value="pickupIndex" :range="pickupOptions" @change="onPickupChange">
            <view class="picker-display">{{ pickupOptions[pickupIndex] }}</view>
          </picker>
        </view>
      </template>
    </template>
  </view>
</template>

<script>
import { get, post, del } from '@/utils/request.js'
import auth from '@/stores/auth.js'

export default {
  data() {
    return {
      items: [],
      loading: true,
      selectedIds: [],
      pickupIndex: 0,
      pickupOptions: ['图书馆门口', '第一食堂门口', '第二食堂门口', '第三教学楼大厅', '宿舍楼A区快递点', '行政楼一楼']
    }
  },
  computed: {
    allSelected: function () {
      return this.items.length > 0 && this.selectedIds.length === this.items.length
    },
    totalAmount: function () {
      var self = this
      var total = 0
      this.items.forEach(function (item) {
        if (self.selectedIds.indexOf(item.book_id) !== -1) {
          total += parseFloat(item.price) || 0
        }
      })
      return total
    }
  },
  mounted: function () {
    if (!auth.isLogin()) {
      uni.showToast({ title: '请先登录', icon: 'none' })
      setTimeout(function () { uni.navigateTo({ url: '/pages/auth/login' }) }, 1000)
      return
    }
    this.fetchCart()
  },
  methods: {
    fetchCart: async function () {
      try {
        var res = await get('/api/cart')
        this.items = res.data.list || []
        this.selectedIds = this.items.map(function (item) { return item.book_id })
      } catch (e) {
        console.log('fetchCart error:', e)
      } finally {
        this.loading = false
      }
    },
    toggleSelect: function (bookId) {
      var idx = this.selectedIds.indexOf(bookId)
      if (idx === -1) {
        this.selectedIds.push(bookId)
      } else {
        this.selectedIds.splice(idx, 1)
      }
    },
    toggleAll: function () {
      if (this.allSelected) {
        this.selectedIds = []
      } else {
        var self = this
        this.selectedIds = this.items.map(function (item) { return item.book_id })
      }
    },
    onRemove: function (id) {
      var self = this
      uni.showModal({
        title: '移除',
        content: '确定移除此书籍？',
        success: async function (res) {
          if (!res.confirm) return
          try {
            await del('/api/cart/' + id)
            uni.showToast({ title: '已移除', icon: 'success' })
            self.fetchCart()
          } catch (e) {
            uni.showToast({ title: e.message || '移除失败', icon: 'none' })
          }
        }
      })
    },
    onClear: function () {
      var self = this
      uni.showModal({
        title: '清空购物车',
        content: '确定清空购物车？此操作不可恢复。',
        success: async function (res) {
          if (!res.confirm) return
          try {
            await del('/api/cart')
            uni.showToast({ title: '购物车已清空', icon: 'success' })
            self.items = []
            self.selectedIds = []
          } catch (e) {
            uni.showToast({ title: e.message || '清空失败', icon: 'none' })
          }
        }
      })
    },
    onPickupChange: function (e) {
      this.pickupIndex = e.detail.value
    },
    onCheckout: async function () {
      if (this.selectedIds.length === 0) {
        uni.showToast({ title: '请至少选择一本教材', icon: 'none' })
        return
      }
      var loc = this.pickupOptions[this.pickupIndex]
      var self = this
      try {
        var res = await post('/api/cart/checkout', {
          book_ids: this.selectedIds,
          pickup_location: loc
        })
        uni.showToast({ title: '下单成功', icon: 'success' })
        setTimeout(function () {
          uni.redirectTo({ url: '/pages/orders/detail?id=' + res.data.order_id })
        }, 800)
      } catch (e) {
        uni.showToast({ title: e.message || '下单失败', icon: 'none' })
      }
    },
    goHome: function () {
      uni.switchTab({ url: '/pages/index/index' })
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.cart-item { padding: 12px 14px; margin-bottom: 10px; }
.item-row { display: flex; align-items: center; }
.cover-wrap { width: 56px; height: 76px; border-radius: 6px; flex-shrink: 0; overflow: hidden; margin: 0 10px; }
.cover-img { width: 100%; height: 100%; }
.cover-placeholder { width: 56px; height: 76px; border-radius: 6px; flex-shrink: 0; background: #cec4b0; margin: 0 10px; }
.item-info { flex: 1; min-width: 0; }
.item-info .title { font-size: 14px; font-weight: 500; color: #2c2416; line-height: 1.4; }
.item-info .author { font-size: 12px; color: #6e6559; margin-top: 2px; display: block; }
.item-info .price { font-size: 16px; font-weight: 700; color: #b49450; margin-top: 4px; display: block; }
.btn-remove { font-size: 12px; color: #6e6559; padding: 4px 10px; border: 1px solid #cec4b0; border-radius: 6px; flex-shrink: 0; }
.bottom-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #ffffff; border-top: 1px solid #cec4b0; display: flex; align-items: center; padding: 10px 16px; padding-bottom: 20px; z-index: 10; }
.select-all { display: flex; align-items: center; font-size: 13px; color: #2c2416; }
.total-row { display: flex; align-items: baseline; margin-left: 8px; }
.total-label { font-size: 12px; color: #6e6559; }
.total-amount { font-size: 18px; font-weight: 700; color: #b49450; margin-left: 4px; }
.btn-group { display: inline-flex; gap: 4px; }
.btn-clear { font-size: 13px; color: #fff; background: linear-gradient(135deg, #c7915c, #d4a574); border-radius: 10px; padding: 10px 16px; }
.checkout-btn { padding: 10px 20px; font-size: 14px; border-radius: 10px; }
.pickup-card { margin-top: 12px; margin-bottom: 70px; padding: 14px; }
.pickup-card .label { font-size: 14px; font-weight: 500; color: #2c2416; display: block; margin-bottom: 8px; }
.picker-display { width: 100%; height: 40px; line-height: 40px; border: 1px solid #cec4b0; border-radius: 8px; padding: 0 12px; font-size: 14px; color: #2c2416; background: #fff; box-sizing: border-box; }
.empty-state { text-align: center; padding: 100px 20px; color: #6e6559; font-size: 14px; }
.empty-icon { font-size: 56px; display: block; margin-bottom: 12px; }
.status-msg { text-align: center; padding: 100px 0; color: #6e6559; }
</style>
