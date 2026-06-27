<template>
  <view class="container">
    <view class="search-bar">
      <view class="search-input-wrap">
        <input v-model="keyword" placeholder="搜索求购..." confirm-type="search" @confirm="onSearch" />
        <view v-if="keyword" class="search-clear" @click.stop="onClear">
          <text>×</text>
        </view>
      </view>
      <text class="search-btn" @click="onSearch">搜索</text>
    </view>

    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else>
      <view v-if="wants.length === 0" class="empty-state">
        <text>暂无求购</text>
        <text class="btn-amber mt-16" style="padding:8px 20px;display:inline-block;" @click="goPost">发布求购</text>
      </view>
      <view v-for="w in wants" :key="w.id" :class="['card', 'want-card', wantAccent(w.status)]" @click="goDetail(w.id)">
        <view class="want-header">
          <text class="title ellipsis">{{ w.title }}</text>
          <text :class="['status-tag', wantStatusClass(w.status)]">{{ w.status_label }}</text>
        </view>
        <view class="want-meta">
          <text v-if="w.author" class="meta-item">{{ w.author }}</text>
          <text v-if="w.publisher" class="meta-item">{{ w.publisher }}</text>
          <text v-if="w.category_name" class="meta-item">{{ w.category_name }}</text>
        </view>
        <view class="want-footer">
          <text class="price">最高 ¥{{ w.max_price }}</text>
          <text class="info">期望 {{ w.condition_label }} · {{ w.fulfiller_count }}人接单</text>
        </view>
      </view>
    </template>

    <view class="float-btn" @click="goPost">
      <text class="float-icon">+</text>
    </view>
  </view>
</template>

<script>
import { get } from '@/utils/request.js'

export default {
  data() {
    return {
      keyword: '',
      wants: [],
      loading: true
    }
  },
  mounted() {
    this.fetchWants()
  },
  methods: {
    async fetchWants() {
      this.loading = true
      try {
        var params = {}
        if (this.keyword) params.keyword = this.keyword
        var res = await get('/api/wants', params)
        this.wants = res.data.list || []
      } catch (e) {
        console.log('fetchWants error:', e)
      } finally {
        this.loading = false
      }
    },
    onSearch() { this.fetchWants() },
    onClear() { this.keyword = ''; this.fetchWants() },
    goDetail(id) { uni.navigateTo({ url: '/pages/wants/detail?id=' + id }) },
    goPost() { uni.navigateTo({ url: '/pages/wants/post' }) },
    wantStatusClass(status) {
      if (status === 'active') return 'status-success'
      return 'status-muted'
    },
    wantAccent(status) {
      if (status === 'active') return 'card-accent-teal'
      return 'card-accent-gray'
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.search-bar { display: flex; align-items: center; margin-bottom: 12px; }
.search-input-wrap { flex: 1; position: relative; }
.search-input-wrap input { width: 100%; height: 40px; border: 1px solid #e5dccf; border-radius: 8px; padding: 0 30px 0 12px; background: #fff; font-size: 14px; box-sizing: border-box; }
.search-clear { position: absolute; right: 4px; top: 50%; transform: translateY(-50%); width: 24px; height: 24px; background: #d4bc7c; border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 2; }
.search-clear text { font-size: 14px; color: #fff; line-height: 1; }
.search-btn { flex-shrink: 0; margin-left: 8px; background: linear-gradient(135deg, #0d9488, #14b8a6); color: #fff; padding: 0 16px; border-radius: 8px; line-height: 40px; font-size: 14px; white-space: nowrap; }
.want-card { padding: 16px; margin-bottom: 10px; }
.want-header { display: flex; justify-content: space-between; }
.title { font-size: 15px; font-weight: 600; color: #2c2416; flex: 1; margin-right: 8px; }
.status-tag { font-size: 11px; padding: 1px 8px; border-radius: 10px; white-space: nowrap; flex-shrink: 0; }
.status-success { background: #f0fdfa; color: #0d9488; }
.status-info { background: rgba(180,148,80,0.08); color: #b49450; }
.status-muted { background: rgba(180,148,80,0.06); color: #8c8478; }
.want-meta { display: flex; flex-wrap: wrap; margin-top: 8px; }
.meta-item { margin-right: 6px; margin-bottom: 4px; }
.meta-item { font-size: 12px; color: #8c8478; background: rgba(180,148,80,0.06); padding: 1px 6px; border-radius: 4px; }
.want-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
.price { font-size: 14px; color: #b49450; font-weight: 600; }
.info { font-size: 12px; color: #8c8478; }
.float-btn { position: fixed; right: 20px; bottom: 80px; width: 50px; height: 50px; background: linear-gradient(135deg, #b49450, #d4bc7c); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(180,148,80,0.4); }
.float-icon { font-size: 28px; color: #fff; line-height: 1; }
.status-msg { text-align: center; padding: 100px 0; color: #8c8478; }
</style>
