<template>
  <view class="container">
    <!-- 未登录 -->
    <view v-if="!auth.isLogin()" class="login-card">
      <view class="card" style="text-align:center;padding:40px 20px;">
        <view class="login-avatar">
          <text class="login-avatar-text">📚</text>
        </view>
        <text class="login-title">校园二手书</text>
        <text class="login-sub">登录后享受便捷的二手教材交易</text>
        <view class="btn-amber mt-20" style="padding:10px 40px;display:inline-block;" @click="goLogin">登录</view>
        <view class="mt-12">
          <text class="link" @click="goRegister">没有账号？去注册</text>
        </view>
      </view>
    </view>

    <!-- 已登录 -->
    <template v-else>
      <!-- 用户头部 -->
      <view class="user-header">
        <view class="header-bg"></view>
        <view class="header-content">
          <view class="avatar">
            <text class="avatar-text">{{ avatarText }}</text>
          </view>
          <text class="user-name">{{ auth.user.name }}</text>
          <text class="user-detail">学号 {{ auth.user.student_id || '未设置' }}</text>
          <text class="user-detail">{{ maskedPhone }}</text>
        </view>
      </view>

      <!-- 订单统计卡片 -->
      <view class="stats-row">
        <view class="stat-card" @click="goOrdersFilter('pending')">
          <text class="stat-num">{{ stats.order_pending }}</text>
          <text class="stat-label">待付款</text>
        </view>
        <view class="stat-card" @click="goOrdersFilter('paid')">
          <text class="stat-num">{{ stats.order_paid }}</text>
          <text class="stat-label">已付款</text>
        </view>
        <view class="stat-card" @click="goOrdersFilter('confirmed')">
          <text class="stat-num">{{ stats.order_confirmed }}</text>
          <text class="stat-label">待取书</text>
        </view>
        <view class="stat-card" @click="goOrdersFilter('picked_up')">
          <text class="stat-num">{{ stats.order_done }}</text>
          <text class="stat-label">已完成</text>
        </view>
      </view>


      <!-- 菜单分组 -->
      <view class="section">
        <text class="section-title">交易管理</text>
        <view class="menu-card card">
          <view class="menu-item" @click="goCart">
            <view class="menu-left">
              <text class="menu-icon">🛒</text>
              <text class="menu-label">购物车</text>
            </view>
            <text class="menu-arrow">›</text>
          </view>
          <view class="divider"></view>
          <view class="menu-item" @click="goOrders">
            <view class="menu-left">
              <text class="menu-icon">📋</text>
              <text class="menu-label">我的订单</text>
            </view>
            <text class="menu-arrow">›</text>
          </view>
          <view class="divider"></view>
          <view class="menu-item" @click="goMySells">
            <view class="menu-left">
              <text class="menu-icon">📖</text>
              <text class="menu-label">我的卖书</text>
            </view>
            <text class="menu-arrow">›</text>
          </view>
          <view class="divider"></view>
          <view class="menu-item" @click="goMyWants">
            <view class="menu-left">
              <text class="menu-icon">🔍</text>
              <text class="menu-label">我的求购</text>
            </view>
            <text class="menu-arrow">›</text>
          </view>
        </view>
      </view>

      <view class="section">
        <text class="section-title">更多</text>
        <view class="menu-card card">
          <view class="menu-item" @click="goSell">
            <view class="menu-left">
              <text class="menu-icon">✏️</text>
              <text class="menu-label">我要卖书</text>
            </view>
            <text class="menu-arrow">›</text>
          </view>
          <view class="divider"></view>
          <view class="menu-item" @click="goPostWant">
            <view class="menu-left">
              <text class="menu-icon">📝</text>
              <text class="menu-label">发布求购</text>
            </view>
            <text class="menu-arrow">›</text>
          </view>
        </view>
      </view>

      <view class="logout-btn" @click="onLogout">退出登录</view>
    </template>
  </view>
</template>

<script>
import auth from '@/stores/auth.js'
import { get, post } from '@/utils/request.js'

export default {
  data() {
    return {
      auth: auth,
      stats: {
        order_pending: 0,
        order_paid: 0,
        order_confirmed: 0,
        order_done: 0,
        my_books_active: 0,
        my_wants_active: 0
      },
      userInfo: null
    }
  },
  computed: {
    avatarText() {
      var name = (this.userInfo && this.userInfo.name) || (this.auth.user && this.auth.user.name) || '?'
      return name.charAt(0)
    },
    maskedPhone() {
      var phone = (this.userInfo && this.userInfo.phone) || (this.auth.user && this.auth.user.phone) || ''
      if (phone.length === 11) {
        return phone.substr(0, 3) + '****' + phone.substr(7)
      }
      return phone
    }
  },
  onShow() {
    if (this.auth.isLogin()) {
      this.fetchUserInfo()
    }
  },
  methods: {
    fetchUserInfo: function () {
      var self = this
      get('/api/auth/me').then(function (res) {
        self.userInfo = res.data
        if (res.data.stats) {
          self.stats = res.data.stats
        }
      }).catch(function (e) {
        console.log('fetch user info error:', e)
        if (e && e.code === 401) {
          auth.logout()
          self.userInfo = null
        }
      })
    },
    goLogin: function () { uni.navigateTo({ url: '/pages/auth/login' }) },
    goRegister: function () { uni.navigateTo({ url: '/pages/auth/register' }) },
    goCart: function () { uni.navigateTo({ url: '/pages/cart/index' }) },
    goOrders: function () { uni.navigateTo({ url: '/pages/orders/index' }) },
    goOrdersFilter: function (status) {
      uni.navigateTo({ url: '/pages/orders/index?status=' + status })
    },
    goMySells: function () { uni.navigateTo({ url: '/pages/my-sells/index' }) },
    goSell: function () { uni.navigateTo({ url: '/pages/books/sell' }) },
    goWants: function () { uni.switchTab({ url: '/pages/wants/index' }) },
    goMyWants: function () { uni.navigateTo({ url: '/pages/wants/mine' }) },
    goPostWant: function () { uni.navigateTo({ url: '/pages/wants/post' }) },
    onLogout: function () {
      var self = this
      uni.showModal({
        title: '提示',
        content: '确定要退出登录吗？',
        success: function (res) {
          if (!res.confirm) return
          post('/api/auth/logout').catch(function (e) {
            console.log('logout error:', e)
          })
          auth.logout()
          self.userInfo = null
          self.stats = {
            order_pending: 0,
            order_paid: 0,
            order_confirmed: 0,
            order_done: 0,
            my_books_active: 0,
            my_wants_active: 0
          }
          uni.showToast({ title: '已退出', icon: 'success' })
        }
      })
    }
  }
}
</script>

<style scoped>
.container { min-height: 100vh; background: #fdfaf4; }

/* 未登录 */
.login-card { padding: 12px; padding-top: 60px; }
.login-avatar {
  width: 72px; height: 72px; border-radius: 50%;
  background: linear-gradient(135deg, #b49450, #b49450);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
}
.login-avatar-text { font-size: 32px; }
.login-title { font-size: 22px; font-weight: 700; color: #2c2416; display: block; }
.login-sub { font-size: 13px; color: #8c8478; margin-top: 8px; display: block; }
.link { font-size: 13px; color: #b49450; }

/* 用户头部 */
.user-header { position: relative; overflow: hidden; }
.header-bg {
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(160deg, #b49450 0%, #b49450 50%, #3b6ba8 100%);
}
.header-content {
  position: relative; z-index: 1;
  display: flex; flex-direction: column; align-items: center;
  padding: 36px 20px 28px;
}
.avatar {
  width: 72px; height: 72px; border-radius: 50%;
  background: rgba(255,255,255,0.18);
  border: 3px solid rgba(255,255,255,0.3);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 14px;
}
.avatar-text { font-size: 30px; color: #fff; font-weight: 700; }
.user-name { font-size: 20px; color: #fff; font-weight: 700; }
.user-detail { font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 4px; }

/* 订单统计 */
.stats-row {
  display: flex; margin: -16px 12px 0;
  position: relative; z-index: 2;
}
.stat-card {
  flex: 1; background: #fffdfa; border-radius: 12px;
  padding: 16px 4px; text-align: center;
  margin: 0 4px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}
.stat-num { font-size: 22px; font-weight: 700; display: block; }
.stat-card:nth-child(1) .stat-num { color: #b0822c; }
.stat-card:nth-child(2) .stat-num { color: #b49450; }
.stat-card:nth-child(3) .stat-num { color: #5b7fbd; }
.stat-card:nth-child(4) .stat-num { color: #2d6a4f; }
.stat-label { font-size: 11px; color: #8c8478; margin-top: 4px; display: block; }

/* 菜单分组 */
.section { margin: 0 12px 12px; }
.section-title { font-size: 12px; color: #8c8478; padding: 8px 4px 8px; display: block; }
.menu-card { padding: 0 16px; }
.menu-item {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 0;
}
.menu-left { display: flex; align-items: center; }
.menu-icon { font-size: 16px; margin-right: 10px; }
.menu-label { font-size: 15px; color: #2c2416; }
.menu-arrow { font-size: 18px; color: #d0cdc8; font-weight: 300; }
.divider { height: 1px; background: #e5dccf; }

/* 退出按钮 */
.logout-btn {
  margin: 20px 12px 40px; text-align: center; padding: 12px;
  border: 1px solid #e5dccf; border-radius: 10px;
  color: #8c8478; font-size: 14px; background: #fff;
}
</style>
