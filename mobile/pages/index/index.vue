<template>
  <view class="container">
    <view class="search-bar">
      <view class="search-input-wrap">
        <input v-model="keyword" placeholder="搜索教材..." confirm-type="search" @confirm="onSearch" />
        <view v-if="keyword" class="search-clear" @click.stop="onClear">
          <text>×</text>
        </view>
      </view>
      <text class="search-btn" @click="onSearch">搜索</text>
    </view>

    <view class="filter-row">
      <view class="dropdown" @click="showDropdown = !showDropdown">
        <text class="dropdown-text">{{ categoryNames[categoryIndex] || '全部学院' }}</text>
        <text class="dropdown-arrow">{{ showDropdown ? '▲' : '▼' }}</text>
      </view>
      <view class="dropdown-list" v-if="showDropdown" catchtouchmove="true">
        <view v-for="(name, idx) in categoryNames" :key="idx"
          :class="['dropdown-item', categoryIndex === idx ? 'active' : '']"
          @click="selectCategory(idx)">
          {{ name }}
        </view>
      </view>
      <view class="dropdown-mask" v-if="showDropdown" @click="showDropdown = false"></view>
    </view>

    <view class="sort-row">
      <view v-for="s in sorts" :key="s.value"
        :class="['sort-item', sortBy === s.value ? 'active' : '']"
        @click="onSortChange(s.value)">
        {{ s.label }}
      </view>
    </view>

    <view class="book-grid">
      <view v-for="(b, idx) in books" :key="b.id" :class="['book-card', cardAccent(idx)]" @click="goDetail(b.id)">
        <safe-image :src="b.cover_img" mode="aspectFill" class="cover" />
        <view class="info">
          <text class="title ellipsis-2">{{ b.title }}</text>
          <text class="author">{{ b.author }}</text>
          <view class="bottom">
            <text class="price">¥{{ b.price }}</text>
            <text class="condition">{{ b.condition_label }}</text>
          </view>
        </view>
      </view>
    </view>

    <view v-if="loading" class="status-msg">加载中...</view>
    <view v-else-if="loadError" class="status-msg error">{{ loadError }}</view>
    <view v-else-if="books.length === 0" class="status-msg">暂无书籍</view>
    <view v-else-if="hasMore" class="load-more" @click="fetchBooks(false)">加载更多</view>
  </view>
</template>

<script>
import { get } from '@/utils/request.js'

export default {
  data() {
    return {
      keyword: '',
      categoryIndex: 0,
      categoryNames: [],
      categories: [],
      showDropdown: false,
      sortBy: 'newest',
      books: [],
      loading: true,
      loadError: '',
      page: 1,
      hasMore: false,
      sorts: [
        { label: '最新', value: 'newest' },
        { label: '价格↑', value: 'price_asc' },
        { label: '价格↓', value: 'price_desc' },
        { label: '成色', value: 'condition' }
      ]
    }
  },
  async mounted() {
    await this.fetchCategories()
    await this.fetchBooks(true)
  },
  methods: {
    async fetchCategories() {
      try {
        var res = await get('/api/categories')
        this.categories = res.data
        this.categoryNames = ['全部学院'].concat(res.data.map(function (c) { return c.name }))
      } catch (e) {
        console.log('fetchCategories error:', e)
      }
    },
    async fetchBooks(isNew) {
      if (isNew) { this.page = 1; this.books = [] }
      this.loading = true
      this.loadError = ''
      try {
        var params = { page: this.page, per_page: 10 }
        if (this.categoryIndex > 0 && this.categories[this.categoryIndex - 1]) {
          params.category_id = this.categories[this.categoryIndex - 1].id
        }
        if (this.keyword) params.keyword = this.keyword
        if (this.sortBy && this.sortBy !== 'newest') params.sort = this.sortBy
        var res = await get('/api/books', params)
        var list = res.data.list || []
        if (isNew) {
          this.books = list
        } else {
          this.books = this.books.concat(list)
        }
        this.page = this.page + 1
        this.hasMore = this.page <= (res.data.meta.last_page || 1)
      } catch (e) {
        console.log('fetchBooks error:', e)
        this.loadError = '加载失败，请下拉刷新'
      } finally {
        this.loading = false
      }
    },
    onSearch() { this.fetchBooks(true) },
    onClear() { this.keyword = ''; this.fetchBooks(true) },
    selectCategory(idx) {
      this.showDropdown = false
      if (idx === this.categoryIndex) return
      this.categoryIndex = idx
      this.fetchBooks(true)
    },
    onSortChange(val) {
      if (val === this.sortBy) return
      this.sortBy = val
      this.fetchBooks(true)
    },
    goDetail(id) { uni.navigateTo({ url: '/pages/books/detail?id=' + id }) },
    cardAccent(idx) {
      var accents = ['card-accent-blue', 'card-accent-amber', 'card-accent-teal', 'card-accent-coral', 'card-accent-indigo']
      return accents[idx % accents.length]
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
.search-btn { flex-shrink: 0; margin-left: 8px; background: linear-gradient(135deg, #b49450, #3b6ba8); color: #fff; padding: 0 16px; border-radius: 8px; line-height: 40px; font-size: 14px; white-space: nowrap; }
.filter-row { position: relative; margin-bottom: 12px; }
.dropdown { height: 40px; line-height: 40px; padding: 0 14px; background: #fff; border: 1px solid #e5dccf; border-radius: 8px; font-size: 14px; color: #2c2416; display: flex; justify-content: space-between; align-items: center; }
.dropdown-text { flex: 1; }
.dropdown-arrow { font-size: 10px; color: #8c8478; margin-left: 8px; }
.dropdown-mask { position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99; }
.dropdown-list { position: absolute; top: 44px; left: 0; right: 0; background: #fff; border: 1px solid #e5dccf; border-radius: 8px; z-index: 100; max-height: 240px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.dropdown-item { height: 42px; line-height: 42px; padding: 0 14px; font-size: 14px; color: #2c2416; border-bottom: 1px solid #e5dccf; }
.dropdown-item:last-child { border-bottom: none; }
.dropdown-item.active { color: #b49450; font-weight: 500; background: #fffdfa; }
.sort-row { display: flex; margin-bottom: 12px; }
.sort-item { padding: 4px 12px; border-radius: 14px; font-size: 12px; color: #8c8478; background: #fff; border: 1px solid #e5dccf; margin-right: 8px; }
.sort-item.active { color: #fff; border-color: transparent; background: linear-gradient(135deg, #b49450, #d4bc7c); }
.book-grid { display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-between; }
.book-card { width: 49%; background: #fffdfa; border-radius: 10px; overflow: hidden; border: 1px solid #e5dccf; margin-bottom: 10px; box-sizing: border-box; }
.book-card:active { transform: scale(0.98); opacity: 0.9; }
.book-card .cover { width: 100%; height: 180px; display: block; }
.book-card .info { padding: 10px; }
.book-card .title { font-size: 14px; font-weight: 500; color: #2c2416; line-height: 1.4; }
.book-card .author { font-size: 12px; color: #8c8478; margin-top: 4px; display: block; }
.book-card .bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 8px; }
.book-card .price { color: #b49450; font-size: 16px; font-weight: 700; }
.book-card .condition { font-size: 11px; color: #2d6a4f; background: #f0fdf4; padding: 2px 6px; border-radius: 4px; }
.status-msg { text-align: center; padding: 60px 0; color: #8c8478; font-size: 14px; }
.status-msg.error { color: #bc4742; }
.load-more { text-align: center; padding: 16px; color: #b49450; font-size: 14px; }
</style>
