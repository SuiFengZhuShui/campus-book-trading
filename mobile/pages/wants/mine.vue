<template>
  <view class="container">
    <view v-if="loading" class="status-msg">加载中...</view>
    <template v-else>
      <view v-if="wants.length === 0" class="empty-state">
        <text>暂无我的求购</text>
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
  </view>
</template>

<script>
import { get } from '@/utils/request.js'

export default {
  data() {
    return {
      wants: [],
      loading: true
    }
  },
  onShow() {
    this.fetchWants()
  },
  methods: {
    async fetchWants() {
      this.loading = true
      try {
        var res = await get('/api/my-wants')
        this.wants = res.data.list || []
      } catch (e) {
        console.log('fetchWants error:', e)
      } finally {
        this.loading = false
      }
    },
    goDetail(id) { uni.navigateTo({ url: '/pages/wants/detail?id=' + id }) },
    goPost() { uni.navigateTo({ url: '/pages/wants/post' }) },
    wantStatusClass(status) {
      if (status === 'active') return 'status-success'
      if (status === 'fulfilled') return 'status-info'
      return 'status-muted'
    },
    wantAccent(status) {
      if (status === 'active') return 'card-accent-teal'
      if (status === 'fulfilled') return 'card-accent-amber'
      return 'card-accent-gray'
    }
  }
}
</script>

<style scoped>
.container { padding: 12px; min-height: 100vh; }
.want-card { padding: 16px; margin-bottom: 10px; }
.want-header { display: flex; justify-content: space-between; }
.title { font-size: 15px; font-weight: 600; color: #2c2416; flex: 1; margin-right: 8px; }
.status-tag { font-size: 11px; padding: 1px 8px; border-radius: 10px; white-space: nowrap; flex-shrink: 0; }
.status-success { background: #f0fdfa; color: #0d9488; }
.status-info { background: rgba(180,148,80,0.08); color: #b49450; }
.status-muted { background: rgba(180,148,80,0.06); color: #8c8478; }
.want-meta { display: flex; flex-wrap: wrap; margin-top: 8px; }
.meta-item { font-size: 12px; color: #8c8478; background: rgba(180,148,80,0.06); padding: 1px 6px; border-radius: 4px; margin-right: 6px; margin-bottom: 4px; }
.want-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 10px; }
.price { font-size: 14px; color: #b49450; font-weight: 600; }
.info { font-size: 12px; color: #8c8478; }
.status-msg { text-align: center; padding: 100px 0; color: #8c8478; }
</style>
