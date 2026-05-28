# Vue 3 Web 前端实现计划

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax.

**Goal:** 用 Vue 3 + Vite + Pinia + Vue Router 4 构建校园二手书 Web 学生端 SPA，逐步替换 Blade 视图。

**Architecture:** SPA 通过 axios 调用 Laravel API（`/api/*`），Token 认证存储在 localStorage，Pinia 管理全局状态，Vue Router 4 做路由守卫。设计系统 CSS token 与 Blade 端完全一致。

**Tech Stack:** Vue 3.4+, Vite 5, Pinia, Vue Router 4, axios, no UI library（手写组件）

---

## 文件结构总览

```
frontend/
├── index.html
├── package.json
├── vite.config.js
├── public/
│   └── favicon.ico
└── src/
    ├── main.js                       # 入口：创建 app + router + pinia
    ├── App.vue                       # 根组件 <router-view>
    ├── api/
    │   ├── request.js                # axios 实例 + 拦截器
    │   ├── auth.js                   # login, register, logout, me
    │   ├── books.js                  # list, detail, submit, myBooks
    │   ├── orders.js                 # create, list, detail, pay, cancel, pickup, review
    │   ├── categories.js             # list
    │   ├── colleges.js               # list, majors, courses
    │   ├── wants.js                  # list, detail, create, fulfill
    │   └── upload.js                 # upload image
    ├── assets/
    │   └── logo.svg                  # 占位 logo
    ├── components/
    │   ├── AppLoading.vue            # loading 骨架屏
    │   ├── AppEmpty.vue              # 空状态占位
    │   ├── AppError.vue              # 错误 + 重试
    │   ├── ConfirmModal.vue          # 确认弹窗
    │   ├── RatingStar.vue            # 星级评分
    │   ├── StatusBadge.vue           # 状态标签
    │   ├── PriceTag.vue              # 价格双行
    │   └── Pagination.vue            # 分页
    ├── composables/
    │   └── useAuth.js                # 认证相关组合函数
    ├── layouts/
    │   └── MainLayout.vue            # AppHeader + <router-view> + AppFooter
    │       ├── AppHeader.vue         # 顶部导航
    │       └── AppFooter.vue         # 底部
    ├── router/
    │   └── index.js                  # 路由配置 + beforeEach 守卫
    ├── stores/
    │   ├── auth.js                   # token, user, login/register/logout/fetchMe
    │   ├── books.js                  # 书籍列表/详情
    │   ├── cart.js                   # 购物车 localStorage + 勾选
    │   ├── orders.js                 # 订单列表/详情
    │   └── wants.js                  # 求购列表/详情
    ├── utils/
    │   └── storage.js                # localStorage 封装
    └── views/
        ├── Home.vue                  # 首页（书籍网格 + 筛选 + 搜索）
        ├── BookDetail.vue            # 书籍详情
        ├── Cart.vue                  # 购物车
        ├── Checkout.vue              # 确认下单
        ├── Orders.vue                # 我的订单列表
        ├── OrderDetail.vue           # 订单详情
        ├── MyBooks.vue               # 我的卖书
        ├── SubmitBook.vue            # 提交卖书
        ├── Wants.vue                 # 求购广场
        ├── WantDetail.vue            # 求购详情
        ├── PostWant.vue              # 发布求购
        ├── Login.vue                 # 登录
        ├── Register.vue              # 注册
        └── Profile.vue              # 个人中心
```

---

## Phase 1: 项目脚手架

### Task 1.1: 创建 Vite + Vue 3 项目

**Files:**
- Create: `frontend/package.json`
- Create: `frontend/vite.config.js`
- Create: `frontend/index.html`
- Create: `frontend/src/main.js`
- Create: `frontend/src/App.vue`

- [ ] **Step 1: 创建 package.json**

```json
{
  "name": "campus-books-frontend",
  "private": true,
  "version": "1.0.0",
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  },
  "dependencies": {
    "vue": "^3.4.0",
    "vue-router": "^4.3.0",
    "pinia": "^2.1.0",
    "axios": "^1.7.0"
  },
  "devDependencies": {
    "@vitejs/plugin-vue": "^5.0.0",
    "vite": "^5.4.0"
  }
}
```

- [ ] **Step 2: 创建 vite.config.js**

```js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true
      }
    }
  }
})
```

- [ ] **Step 3: 创建 index.html**

```html
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>校园二手书</title>
</head>
<body>
  <div id="app"></div>
  <script type="module" src="/src/main.js"></script>
</body>
</html>
```

- [ ] **Step 4: 创建 src/main.js**

```js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
```

- [ ] **Step 5: 创建 src/App.vue**

```vue
<template>
  <router-view />
</template>
```

- [ ] **Step 6: 安装依赖**

```bash
cd frontend && npm install
```

- [ ] **Step 7: 验证 dev server 启动**

```bash
npm run dev
```
Expected: Vite dev server 在 http://localhost:3000 启动。

---

### Task 1.2: CSS 设计 Token & 全局样式

**Files:**
- Create: `frontend/src/assets/tokens.css`
- Modify: `frontend/src/main.js`

- [ ] **Step 1: 创建 src/assets/tokens.css**

```css
:root {
  --color-deep-blue: #1e3a5f;
  --color-deep-blue-light: #2c5282;
  --color-amber: #c7915c;
  --color-amber-light: #d4a574;
  --color-warm-bg: #faf8f5;
  --color-warm-card: #fffdfa;
  --color-ink: #1a1f2b;
  --color-warm-gray: #6b6d76;
  --color-warm-gray-light: #9d9fa8;
  --color-border: #e0dcd5;
  --color-border-light: #f0ede7;
  --color-success: #2d6a4f;
  --color-danger: #bc4742;
  --color-warning: #b0822c;
  --color-info: #2c5282;

  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-full: 9999px;

  --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
  --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
  --shadow-hover: 0 6px 24px rgba(0,0,0,0.12);

  --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Microsoft YaHei', sans-serif;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: var(--font-sans);
  background: var(--color-warm-bg);
  color: var(--color-ink);
  -webkit-font-smoothing: antialiased;
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='60' height='60' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
}

a {
  color: var(--color-amber);
  text-decoration: none;
}

.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 24px;
  background: var(--color-amber);
  color: #fff;
  border: none;
  border-radius: var(--radius-md);
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
}
.btn-primary:hover { background: var(--color-amber-light); box-shadow: var(--shadow-md); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 9px 23px;
  background: transparent;
  color: var(--color-deep-blue);
  border: 1px solid var(--color-deep-blue);
  border-radius: var(--radius-md);
  font-size: 15px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-outline:hover { background: var(--color-deep-blue); color: #fff; }

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}
```

- [ ] **Step 2: 在 main.js 中导入 tokens.css**

Modify `frontend/src/main.js`，在创建 app 之前添加：

```js
import './assets/tokens.css'
```

---

### Task 1.3: localStorage 工具

**Files:**
- Create: `frontend/src/utils/storage.js`

- [ ] **Step 1: 创建 src/utils/storage.js**

```js
const PREFIX = 'campus_books_'

export function get(key) {
  try {
    const raw = localStorage.getItem(PREFIX + key)
    return raw ? JSON.parse(raw) : null
  } catch {
    return null
  }
}

export function set(key, value) {
  localStorage.setItem(PREFIX + key, JSON.stringify(value))
}

export function remove(key) {
  localStorage.removeItem(PREFIX + key)
}
```

---

### Task 1.4: axios API 层

**Files:**
- Create: `frontend/src/api/request.js`
- Create: `frontend/src/api/auth.js`
- Create: `frontend/src/api/books.js`
- Create: `frontend/src/api/orders.js`
- Create: `frontend/src/api/categories.js`
- Create: `frontend/src/api/colleges.js`
- Create: `frontend/src/api/wants.js`
- Create: `frontend/src/api/upload.js`

- [ ] **Step 1: 创建 src/api/request.js**

```js
import axios from 'axios'
import { get, remove } from '@/utils/storage'

const request = axios.create({
  baseURL: '/api',
  timeout: 15000,
  headers: { 'Content-Type': 'application/json' }
})

request.interceptors.request.use(config => {
  const token = get('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

request.interceptors.response.use(
  response => {
    const { code, message, data } = response.data
    if (code === 401) {
      remove('token')
      remove('user')
      window.location.href = '/login'
      return Promise.reject(new Error('未登录'))
    }
    if (code !== 200) {
      return Promise.reject(new Error(message || '请求失败'))
    }
    return data
  },
  error => {
    const msg = error.response?.data?.message || error.message || '网络错误'
    return Promise.reject(new Error(msg))
  }
)

export default request
```

- [ ] **Step 2: 创建 src/api/auth.js**

```js
import request from './request'

export function login(phone, password) {
  return request.post('/auth/login', { phone, password })
}

export function register(data) {
  return request.post('/auth/register', data)
}

export function logout() {
  return request.post('/auth/logout')
}

export function me() {
  return request.get('/auth/me')
}
```

- [ ] **Step 3: 创建 src/api/books.js**

```js
import request from './request'

export function getBookList(params) {
  return request.get('/books', { params })
}

export function getBookDetail(id) {
  return request.get(`/books/${id}`)
}

export function submitBook(formData) {
  // formData is a FormData instance for file upload
  return request.post('/books/submit', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}

export function getMyBooks(params) {
  return request.get('/my-books', { params })
}
```

- [ ] **Step 4: 创建 src/api/orders.js**

```js
import request from './request'

export function createOrder(data) {
  return request.post('/orders', data)
}

export function getOrderList(params) {
  return request.get('/orders', { params })
}

export function getOrderDetail(id) {
  return request.get(`/orders/${id}`)
}

export function payOrder(id) {
  return request.post(`/orders/${id}/pay`)
}

export function cancelOrder(id, reason) {
  return request.post(`/orders/${id}/cancel`, { reason })
}

export function pickupOrder(id) {
  return request.post(`/orders/${id}/pickup`)
}

export function reviewOrder(id, data) {
  return request.post(`/orders/${id}/review`, data)
}
```

- [ ] **Step 5: 创建 src/api/categories.js**

```js
import request from './request'

export function getCategories() {
  return request.get('/categories')
}
```

- [ ] **Step 6: 创建 src/api/colleges.js**

```js
import request from './request'

export function getColleges() {
  return request.get('/colleges')
}

export function getMajors(collegeId) {
  return request.get(`/colleges/${collegeId}/majors`)
}

export function getCourses(majorId) {
  return request.get(`/majors/${majorId}/courses`)
}
```

- [ ] **Step 7: 创建 src/api/wants.js**

```js
import request from './request'

export function getWantList(params) {
  return request.get('/wants', { params })
}

export function getWantDetail(id) {
  return request.get(`/wants/${id}`)
}

export function createWant(data) {
  return request.post('/wants', data)
}

export function fulfillWant(id) {
  return request.post(`/wants/${id}/fulfill`)
}
```

- [ ] **Step 8: 创建 src/api/upload.js**

```js
import request from './request'

export function uploadImage(file) {
  const formData = new FormData()
  formData.append('file', file)
  return request.post('/upload', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })
}
```

---

## Phase 2: 路由 + Store + 布局

### Task 2.1: Vue Router 配置

**Files:**
- Create: `frontend/src/router/index.js`

- [ ] **Step 1: 创建 src/router/index.js**

```js
import { createRouter, createWebHistory } from 'vue-router'
import { get } from '@/utils/storage'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/Register.vue'),
    meta: { guest: true }
  },
  {
    path: '/',
    component: () => import('@/layouts/MainLayout.vue'),
    children: [
      { path: '', name: 'Home', component: () => import('@/views/Home.vue') },
      // 搜索复用首页 + keyword query param
      { path: 'books/:id', name: 'BookDetail', component: () => import('@/views/BookDetail.vue') },
      { path: 'cart', name: 'Cart', component: () => import('@/views/Cart.vue') },
      { path: 'checkout', name: 'Checkout', component: () => import('@/views/Checkout.vue'), meta: { auth: true } },
      { path: 'orders', name: 'Orders', component: () => import('@/views/Orders.vue'), meta: { auth: true } },
      { path: 'orders/:id', name: 'OrderDetail', component: () => import('@/views/OrderDetail.vue'), meta: { auth: true } },
      { path: 'my-books', name: 'MyBooks', component: () => import('@/views/MyBooks.vue'), meta: { auth: true } },
      { path: 'submit-book', name: 'SubmitBook', component: () => import('@/views/SubmitBook.vue'), meta: { auth: true } },
      { path: 'wants', name: 'Wants', component: () => import('@/views/Wants.vue') },
      { path: 'wants/:id', name: 'WantDetail', component: () => import('@/views/WantDetail.vue') },
      { path: 'post-want', name: 'PostWant', component: () => import('@/views/PostWant.vue'), meta: { auth: true } },
      { path: 'profile', name: 'Profile', component: () => import('@/views/Profile.vue'), meta: { auth: true } }
    ]
  },
  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  const token = get('token')

  if (to.meta.auth && !token) {
    return next({ name: 'Login', query: { redirect: to.fullPath } })
  }

  if (to.meta.guest && token) {
    return next({ name: 'Home' })
  }

  next()
})

export default router
```

---

### Task 2.2: Pinia Auth Store

**Files:**
- Create: `frontend/src/stores/auth.js`

- [ ] **Step 1: 创建 src/stores/auth.js**

```js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import * as authApi from '@/api/auth'
import { get, set, remove } from '@/utils/storage'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(get('token') || '')
  const user = ref(get('user') || null)

  const isLoggedIn = computed(() => !!token.value)

  async function loginAction(phone, password) {
    const data = await authApi.login(phone, password)
    token.value = data.token
    user.value = data.user
    set('token', data.token)
    set('user', data.user)
    return data
  }

  async function registerAction(form) {
    const data = await authApi.register(form)
    token.value = data.token
    user.value = data.user
    set('token', data.token)
    set('user', data.user)
    return data
  }

  async function fetchMe() {
    const data = await authApi.me()
    user.value = data
    set('user', data)
  }

  async function logoutAction() {
    try { await authApi.logout() } catch {}
    token.value = ''
    user.value = null
    remove('token')
    remove('user')
  }

  return { token, user, isLoggedIn, loginAction, registerAction, fetchMe, logoutAction }
})
```

---

### Task 2.3: Pinia Cart Store

**Files:**
- Create: `frontend/src/stores/cart.js`

- [ ] **Step 1: 创建 src/stores/cart.js**

```js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { get, set } from '@/utils/storage'

export const useCartStore = defineStore('cart', () => {
  const items = ref(get('cart') || [])
  // item: { book_id, title, cover_img, price, condition_label }

  const count = computed(() => items.value.length)
  const checkedIds = ref([])
  const checkedItems = computed(() => items.value.filter(i => checkedIds.value.includes(i.book_id)))
  const totalAmount = computed(() => checkedItems.value.reduce((sum, i) => sum + Number(i.price), 0))

  function persist() {
    set('cart', items.value)
  }

  function addItem(book) {
    if (items.value.find(i => i.book_id === book.id)) return
    items.value.push({
      book_id: book.id,
      title: book.title,
      cover_img: book.cover_img,
      price: book.price,
      condition_label: book.condition_label
    })
    persist()
  }

  function removeItem(bookId) {
    items.value = items.value.filter(i => i.book_id !== bookId)
    checkedIds.value = checkedIds.value.filter(id => id !== bookId)
    persist()
  }

  function toggleCheck(bookId) {
    const idx = checkedIds.value.indexOf(bookId)
    if (idx > -1) checkedIds.value.splice(idx, 1)
    else checkedIds.value.push(bookId)
  }

  function checkAll() {
    if (checkedIds.value.length === items.value.length) {
      checkedIds.value = []
    } else {
      checkedIds.value = items.value.map(i => i.book_id)
    }
  }

  function clearChecked() {
    items.value = items.value.filter(i => !checkedIds.value.includes(i.book_id))
    checkedIds.value = []
    persist()
  }

  return { items, count, checkedIds, checkedItems, totalAmount, addItem, removeItem, toggleCheck, checkAll, clearChecked }
})
```

---

### Task 2.4: 布局组件

**Files:**
- Create: `frontend/src/layouts/MainLayout.vue`
- Create: `frontend/src/layouts/AppHeader.vue`
- Create: `frontend/src/layouts/AppFooter.vue`

- [ ] **Step 1: 创建 src/layouts/AppHeader.vue**

```vue
<template>
  <header class="app-header">
    <div class="header-inner container">
      <router-link to="/" class="logo">校园二手书</router-link>

      <div class="header-right">
        <router-link to="/sell" class="nav-link">卖书</router-link>

        <template v-if="auth.isLoggedIn">
          <router-link to="/orders" class="nav-link">我的订单</router-link>
          <router-link to="/my-books" class="nav-link">我的卖书</router-link>
          <router-link to="/cart" class="cart-link">
            🛒 <span v-if="cart.count" class="cart-badge">{{ cart.count }}</span>
          </router-link>
          <div class="user-menu" @click="toggleDropdown">
            <span class="user-name">{{ auth.user?.name || '用户' }}</span>
            <div v-if="showDropdown" class="dropdown">
              <router-link to="/profile" @click="showDropdown = false">个人中心</router-link>
              <router-link to="/orders" @click="showDropdown = false">我的订单</router-link>
              <router-link to="/my-books" @click="showDropdown = false">我的卖书</router-link>
              <a href="#" @click.prevent="handleLogout">退出登录</a>
            </div>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="nav-link">登录</router-link>
          <router-link to="/register" class="btn-primary btn-sm">注册</router-link>
        </template>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const auth = useAuthStore()
const cart = useCartStore()
const router = useRouter()
const showDropdown = ref(false)

function toggleDropdown() { showDropdown.value = !showDropdown.value }

async function handleLogout() {
  await auth.logoutAction()
  showDropdown.value = false
  router.push('/')
}
</script>

<style scoped>
.app-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(30, 58, 95, 0.92);
  backdrop-filter: blur(12px);
  color: #fff;
}
.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 56px;
}
.logo { color: #fff; font-size: 20px; font-weight: 700; }
.header-right { display: flex; align-items: center; gap: 18px; }
.nav-link { color: rgba(255,255,255,0.85); font-size: 14px; transition: color 0.2s; }
.nav-link:hover { color: var(--color-amber); }
.cart-link { color: #fff; position: relative; font-size: 18px; }
.cart-badge {
  position: absolute; top: -8px; right: -10px;
  background: var(--color-amber); color: #fff; font-size: 11px;
  width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
}
.user-menu { position: relative; cursor: pointer; }
.user-name { font-size: 14px; }
.dropdown {
  position: absolute; top: 100%; right: 0; margin-top: 8px;
  background: #fff; border-radius: var(--radius-md); box-shadow: var(--shadow-md);
  min-width: 140px; padding: 8px 0;
}
.dropdown a {
  display: block; padding: 8px 16px; color: var(--color-ink); font-size: 14px;
  transition: background 0.2s;
}
.dropdown a:hover { background: var(--color-border-light); }
.btn-sm { padding: 6px 16px; font-size: 13px; }
</style>
```

- [ ] **Step 2: 创建 src/layouts/AppFooter.vue**

```vue
<template>
  <footer class="app-footer">
    <div class="container">
      <p>校园二手书 &copy; 2026 — 让知识在校园流转</p>
    </div>
  </footer>
</template>

<style scoped>
.app-footer {
  margin-top: 60px;
  padding: 24px 0;
  text-align: center;
  color: var(--color-warm-gray-light);
  font-size: 13px;
  border-top: 1px solid var(--color-border-light);
}
</style>
```

- [ ] **Step 3: 创建 src/layouts/MainLayout.vue**

```vue
<template>
  <div class="main-layout">
    <AppHeader />
    <main class="main-content container">
      <router-view />
    </main>
    <AppFooter />
  </div>
</template>

<script setup>
import AppHeader from './AppHeader.vue'
import AppFooter from './AppFooter.vue'
</script>

<style scoped>
.main-content { min-height: calc(100vh - 56px - 80px); padding-top: 24px; }
</style>
```

---

## Phase 3: 全局共享组件

### Task 3.1: AppLoading / AppEmpty / AppError

**Files:**
- Create: `frontend/src/components/AppLoading.vue`
- Create: `frontend/src/components/AppEmpty.vue`
- Create: `frontend/src/components/AppError.vue`

- [ ] **Step 1: 创建 src/components/AppLoading.vue**

```vue
<template>
  <div class="app-loading">
    <div class="spinner"></div>
    <p v-if="text">{{ text }}</p>
  </div>
</template>

<script setup>
defineProps({ text: { type: String, default: '加载中...' } })
</script>

<style scoped>
.app-loading { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 0; color: var(--color-warm-gray); }
.spinner {
  width: 36px; height: 36px; border: 3px solid var(--color-border);
  border-top-color: var(--color-amber); border-radius: 50%;
  animation: spin 0.8s linear infinite; margin-bottom: 12px;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
```

- [ ] **Step 2: 创建 src/components/AppEmpty.vue**

```vue
<template>
  <div class="app-empty">
    <p class="empty-text">{{ text }}</p>
    <router-link v-if="link" :to="link" class="btn-primary">{{ linkText }}</router-link>
  </div>
</template>

<script setup>
defineProps({
  text: { type: String, default: '暂无数据' },
  link: { type: [String, Object], default: '' },
  linkText: { type: String, default: '去逛逛' }
})
</script>

<style scoped>
.app-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 0; }
.empty-text { color: var(--color-warm-gray); font-size: 15px; margin-bottom: 16px; }
</style>
```

- [ ] **Step 3: 创建 src/components/AppError.vue**

```vue
<template>
  <div class="app-error">
    <p>{{ message }}</p>
    <button v-if="retry" class="btn-outline" @click="$emit('retry')">重试</button>
  </div>
</template>

<script setup>
defineProps({
  message: { type: String, default: '加载失败，请稍后再试' },
  retry: { type: Boolean, default: true }
})
defineEmits(['retry'])
</script>

<style scoped>
.app-error { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 0; gap: 16px; color: var(--color-danger); font-size: 15px; }
</style>
```

---

### Task 3.2: StatusBadge / PriceTag / RatingStar

**Files:**
- Create: `frontend/src/components/StatusBadge.vue`
- Create: `frontend/src/components/PriceTag.vue`
- Create: `frontend/src/components/RatingStar.vue`

- [ ] **Step 1: 创建 src/components/StatusBadge.vue**

```vue
<template>
  <span class="status-badge" :class="colorClass">{{ label }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: { type: String, required: true },
  label: { type: String, default: '' }
})

const colorMap = {
  pending: 'badge-yellow', paid: 'badge-blue', confirmed: 'badge-green',
  picked_up: 'badge-green', completed: 'badge-gray', cancelled: 'badge-red',
  pending_review: 'badge-yellow', approved: 'badge-blue', active: 'badge-green',
  sold: 'badge-blue', removed: 'badge-red', active_want: 'badge-green',
  fulfilled: 'badge-blue', expired: 'badge-gray', closed: 'badge-red'
}

const colorClass = computed(() => colorMap[props.status] || 'badge-gray')
</script>

<style scoped>
.status-badge {
  display: inline-block; padding: 2px 10px; border-radius: var(--radius-full);
  font-size: 12px; font-weight: 500; white-space: nowrap;
}
.badge-yellow { background: #fef3c7; color: #92400e; }
.badge-blue   { background: #dbeafe; color: #1e40af; }
.badge-green  { background: #d1fae5; color: #065f46; }
.badge-red    { background: #fee2e2; color: #991b1b; }
.badge-gray   { background: #f3f4f6; color: #6b7280; }
</style>
```

- [ ] **Step 2: 创建 src/components/PriceTag.vue**

```vue
<template>
  <span class="price-tag">
    <span class="original">¥{{ original }}</span>
    <span class="current">¥{{ current }}</span>
  </span>
</template>

<script setup>
defineProps({
  original: { type: [String, Number], required: true },
  current: { type: [String, Number], required: true }
})
</script>

<style scoped>
.price-tag { display: flex; align-items: baseline; gap: 8px; }
.original { color: var(--color-warm-gray); text-decoration: line-through; font-size: 13px; }
.current { color: var(--color-amber); font-size: 20px; font-weight: 700; }
</style>
```

- [ ] **Step 3: 创建 src/components/RatingStar.vue**

```vue
<template>
  <span class="rating-star">
    <span v-for="i in 5" :key="i" :class="i <= rating ? 'star filled' : 'star'" @click="$emit('rate', i)">★</span>
    <span v-if="showScore" class="score">{{ rating.toFixed(1) }}</span>
  </span>
</template>

<script setup>
defineProps({
  rating: { type: Number, default: 0 },
  showScore: { type: Boolean, default: false }
})
defineEmits(['rate'])
</script>

<style scoped>
.rating-star { display: inline-flex; align-items: center; }
.star { color: #d1d5db; font-size: 16px; cursor: default; }
.star.filled { color: #f59e0b; }
.score { margin-left: 6px; font-size: 14px; color: var(--color-warm-gray); }
</style>
```

---

### Task 3.3: ConfirmModal / Pagination

**Files:**
- Create: `frontend/src/components/ConfirmModal.vue`
- Create: `frontend/src/components/Pagination.vue`

- [ ] **Step 1: 创建 src/components/ConfirmModal.vue**

```vue
<template>
  <Teleport to="body">
    <div v-if="visible" class="modal-overlay" @click.self="$emit('cancel')">
      <div class="modal-box">
        <p class="modal-text">{{ text }}</p>
        <slot />
        <div class="modal-actions">
          <button class="btn-outline" @click="$emit('cancel')">{{ cancelText }}</button>
          <button class="btn-primary" @click="$emit('confirm')">{{ confirmText }}</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
defineProps({
  visible: { type: Boolean, default: false },
  text: { type: String, default: '确定要执行此操作吗？' },
  confirmText: { type: String, default: '确定' },
  cancelText: { type: String, default: '取消' }
})
defineEmits(['confirm', 'cancel'])
</script>

<style scoped>
.modal-overlay {
  position: fixed; inset: 0; z-index: 1000;
  background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center;
}
.modal-box {
  background: #fff; border-radius: var(--radius-lg); padding: 32px; min-width: 360px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}
.modal-text { font-size: 16px; margin-bottom: 24px; text-align: center; }
.modal-actions { display: flex; gap: 12px; justify-content: center; }
</style>
```

- [ ] **Step 2: 创建 src/components/Pagination.vue**

```vue
<template>
  <div v-if="total > 0" class="pagination">
    <button :disabled="current <= 1" @click="go(current - 1)">&lt;</button>
    <template v-for="p in pages" :key="p">
      <span v-if="p === '...'" class="ellipsis">...</span>
      <button v-else :class="{ active: p === current }" @click="go(p)">{{ p }}</button>
    </template>
    <button :disabled="current >= last" @click="go(current + 1)">&gt;</button>
    <span class="total-info">共 {{ total }} 条</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  current: { type: Number, required: true },
  last: { type: Number, required: true },
  total: { type: Number, required: true }
})
const emit = defineEmits(['change'])

const pages = computed(() => {
  const c = props.current, l = props.last
  if (l <= 7) return Array.from({ length: l }, (_, i) => i + 1)
  if (c <= 3) return [1, 2, 3, 4, '...', l]
  if (c >= l - 2) return [1, '...', l - 3, l - 2, l - 1, l]
  return [1, '...', c - 1, c, c + 1, '...', l]
})

function go(page) { if (page !== props.current) emit('change', page) }
</script>

<style scoped>
.pagination { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 24px 0; }
.pagination button {
  min-width: 36px; height: 36px; border: 1px solid var(--color-border); background: #fff;
  border-radius: var(--radius-sm); cursor: pointer; font-size: 14px; color: var(--color-ink);
}
.pagination button:hover { border-color: var(--color-amber); color: var(--color-amber); }
.pagination button.active { background: var(--color-amber); color: #fff; border-color: var(--color-amber); }
.pagination button:disabled { opacity: 0.4; cursor: not-allowed; }
.ellipsis { padding: 0 4px; color: var(--color-warm-gray); }
.total-info { margin-left: 12px; font-size: 13px; color: var(--color-warm-gray); }
</style>
```

---

## Phase 4: 认证页面

### Task 4.1: 登录页

**Files:**
- Create: `frontend/src/views/Login.vue`

- [ ] **Step 1: 创建 src/views/Login.vue**

```vue
<template>
  <div class="auth-page">
    <div class="auth-card">
      <router-link to="/" class="auth-logo">校园二手书</router-link>
      <h2>学生登录</h2>

      <div v-if="errorMsg" class="alert-error">{{ errorMsg }}</div>

      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>手机号</label>
          <input v-model="form.phone" type="text" maxlength="11" placeholder="请输入11位手机号" />
          <p v-if="errors.phone" class="field-error">{{ errors.phone }}</p>
        </div>
        <div class="form-group">
          <label>密码</label>
          <input v-model="form.password" type="password" placeholder="请输入密码" />
          <p v-if="errors.password" class="field-error">{{ errors.password }}</p>
        </div>
        <button type="submit" class="btn-primary btn-block" :disabled="submitting">
          {{ submitting ? '登录中...' : '登录' }}
        </button>
      </form>

      <p class="auth-footer">
        还没有账号？<router-link to="/register">去注册</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const form = reactive({ phone: '', password: '' })
const errors = reactive({ phone: '', password: '' })
const errorMsg = ref('')
const submitting = ref(false)

function validate() {
  errors.phone = form.phone.length !== 11 ? '请输入11位手机号' : ''
  errors.password = form.password.length < 6 ? '密码至少6位' : ''
  return !errors.phone && !errors.password
}

async function handleLogin() {
  if (!validate()) return
  submitting.value = true
  errorMsg.value = ''
  try {
    await auth.loginAction(form.phone, form.password)
    const redirect = route.query.redirect || '/'
    router.push(redirect)
  } catch (e) {
    errorMsg.value = e.message || '手机号或密码错误'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh; display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, var(--color-deep-blue) 0%, #2c5282 100%);
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence baseFrequency='0.5'/%3E%3C/filter%3E%3Crect width='60' height='60' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
}
.auth-card {
  background: var(--color-warm-card); border-radius: var(--radius-lg);
  padding: 48px 40px; width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.auth-logo { display: block; text-align: center; font-size: 22px; font-weight: 700; color: var(--color-deep-blue); margin-bottom: 8px; }
h2 { text-align: center; font-size: 18px; color: var(--color-warm-gray); margin-bottom: 32px; font-weight: 400; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: var(--color-ink); }
.form-group input {
  width: 100%; height: 42px; padding: 0 14px;
  border: 1px solid var(--color-border); border-radius: var(--radius-md);
  font-size: 15px; background: #fff; transition: border-color 0.2s;
}
.form-group input:focus { outline: none; border-color: var(--color-amber); box-shadow: 0 0 0 3px rgba(199,145,92,0.15); }
.field-error { color: var(--color-danger); font-size: 13px; margin-top: 4px; }
.alert-error {
  background: #fee2e2; color: var(--color-danger); padding: 10px 14px;
  border-radius: var(--radius-md); margin-bottom: 20px; font-size: 14px;
}
.btn-block { width: 100%; height: 44px; font-size: 16px; margin-top: 8px; }
.auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: var(--color-warm-gray); }
</style>
```

---

### Task 4.2: 注册页

**Files:**
- Create: `frontend/src/views/Register.vue`

- [ ] **Step 1: 创建 src/views/Register.vue**

```vue
<template>
  <div class="auth-page">
    <div class="auth-card">
      <router-link to="/" class="auth-logo">校园二手书</router-link>
      <h2>学生注册</h2>

      <div v-if="errorMsg" class="alert-error">{{ errorMsg }}</div>

      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label>学号</label>
          <input v-model="form.student_id" type="text" placeholder="请输入学号" />
          <p v-if="errors.student_id" class="field-error">{{ errors.student_id }}</p>
        </div>
        <div class="form-group">
          <label>姓名</label>
          <input v-model="form.name" type="text" placeholder="请输入姓名" />
          <p v-if="errors.name" class="field-error">{{ errors.name }}</p>
        </div>
        <div class="form-group">
          <label>手机号</label>
          <input v-model="form.phone" type="text" maxlength="11" placeholder="请输入11位手机号" />
          <p v-if="errors.phone" class="field-error">{{ errors.phone }}</p>
        </div>
        <div class="form-group">
          <label>密码</label>
          <input v-model="form.password" type="password" placeholder="至少8位，含大小写字母和数字" />
          <p v-if="errors.password" class="field-error">{{ errors.password }}</p>
        </div>
        <div class="form-group">
          <label>确认密码</label>
          <input v-model="form.password_confirmation" type="password" placeholder="请再次输入密码" />
          <p v-if="errors.password_confirmation" class="field-error">{{ errors.password_confirmation }}</p>
        </div>
        <button type="submit" class="btn-primary btn-block" :disabled="submitting">
          {{ submitting ? '注册中...' : '注册' }}
        </button>
      </form>

      <p class="auth-footer">
        已有账号？<router-link to="/login">去登录</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  student_id: '', name: '', phone: '', password: '', password_confirmation: ''
})
const errors = reactive({
  student_id: '', name: '', phone: '', password: '', password_confirmation: ''
})
const errorMsg = ref('')
const submitting = ref(false)

function validate() {
  errors.student_id = form.student_id ? '' : '请输入学号'
  errors.name = form.name.length >= 2 ? '' : '姓名至少2个字符'
  errors.phone = /^1\d{10}$/.test(form.phone) ? '' : '请输入正确的手机号'
  errors.password = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(form.password)
    ? '' : '至少8位，含大小写字母和数字'
  errors.password_confirmation = form.password === form.password_confirmation ? '' : '两次密码不一致'
  return Object.values(errors).every(e => !e)
}

async function handleRegister() {
  if (!validate()) return
  submitting.value = true
  errorMsg.value = ''
  try {
    await auth.registerAction(form)
    router.push('/')
  } catch (e) {
    errorMsg.value = e.message || '注册失败，请稍后再试'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
/* 复用登录页样式 */
.auth-page {
  min-height: 100vh; display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, var(--color-deep-blue) 0%, #2c5282 100%);
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence baseFrequency='0.5'/%3E%3C/filter%3E%3Crect width='60' height='60' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
}
.auth-card {
  background: var(--color-warm-card); border-radius: var(--radius-lg);
  padding: 48px 40px; width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}
.auth-logo { display: block; text-align: center; font-size: 22px; font-weight: 700; color: var(--color-deep-blue); margin-bottom: 8px; }
h2 { text-align: center; font-size: 18px; color: var(--color-warm-gray); margin-bottom: 24px; font-weight: 400; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: var(--color-ink); }
.form-group input {
  width: 100%; height: 42px; padding: 0 14px;
  border: 1px solid var(--color-border); border-radius: var(--radius-md);
  font-size: 15px; background: #fff; transition: border-color 0.2s;
}
.form-group input:focus { outline: none; border-color: var(--color-amber); box-shadow: 0 0 0 3px rgba(199,145,92,0.15); }
.field-error { color: var(--color-danger); font-size: 13px; margin-top: 4px; }
.alert-error {
  background: #fee2e2; color: var(--color-danger); padding: 10px 14px;
  border-radius: var(--radius-md); margin-bottom: 20px; font-size: 14px;
}
.btn-block { width: 100%; height: 44px; font-size: 16px; margin-top: 8px; }
.auth-footer { text-align: center; margin-top: 20px; font-size: 14px; color: var(--color-warm-gray); }
</style>
```

---

## Phase 5: 首页 + 书籍详情

### Task 5.1: 首页

**Files:**
- Create: `frontend/src/views/Home.vue`

- [ ] **Step 1: 创建 src/views/Home.vue**

```vue
<template>
  <div class="home-page">
    <!-- 搜索栏 -->
    <div class="search-bar">
      <input v-model="keyword" type="text" placeholder="搜索书名、作者..." @keyup.enter="doSearch" />
      <button class="btn-primary" @click="doSearch">搜索</button>
    </div>

    <!-- 学院筛选下拉 -->
    <div class="filter-bar">
      <select v-model="collegeId" @change="onCollegeChange">
        <option value="">全部学院</option>
        <option v-for="c in colleges" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <select v-model="majorId" @change="onMajorChange" :disabled="!collegeId">
        <option value="">全部专业</option>
        <option v-for="m in majors" :key="m.id" :value="m.id">{{ m.name }}</option>
      </select>
      <select v-model="courseId" :disabled="!majorId">
        <option value="">全部课程</option>
        <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <div class="result-count">共 {{ meta.total }} 本书</div>
    </div>

    <!-- 状态覆盖 -->
    <AppLoading v-if="loading" />
    <AppError v-else-if="error" :message="error" @retry="fetchBooks" />
    <AppEmpty v-else-if="meta.total === 0" text="暂无在售书籍" :link="{ name: 'Wants' }" linkText="去求购广场" />
    <template v-else>
      <div class="book-grid">
        <div v-for="book in books" :key="book.id" class="book-card" @click="$router.push(`/books/${book.id}`)">
          <div class="cover-wrap">
            <img v-if="book.cover_img" :src="book.cover_img" :alt="book.title" />
            <div v-else class="cover-placeholder">{{ book.title[0] }}</div>
          </div>
          <div class="card-info">
            <h3 class="book-title">{{ book.title }}</h3>
            <p class="book-meta">{{ book.author }} · {{ book.publisher }}</p>
            <div class="card-bottom">
              <StatusBadge :status="book.condition" :label="book.condition_label" />
              <span class="price">¥{{ book.price }}</span>
            </div>
          </div>
        </div>
      </div>
      <Pagination :current="meta.current_page" :last="meta.last_page" :total="meta.total" @change="onPageChange" />
    </template>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getBookList } from '@/api/books'
import { getColleges, getMajors, getCourses } from '@/api/colleges'
import AppLoading from '@/components/AppLoading.vue'
import AppError from '@/components/AppError.vue'
import AppEmpty from '@/components/AppEmpty.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Pagination from '@/components/Pagination.vue'

const route = useRoute()
const router = useRouter()

const keyword = ref(route.query.keyword || '')
const collegeId = ref('')
const majorId = ref('')
const courseId = ref('')
const colleges = ref([])
const majors = ref([])
const courses = ref([])
const books = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const loading = ref(true)
const error = ref('')
const page = ref(1)

async function fetchColleges() {
  try { colleges.value = await getColleges() } catch {}
}
async function onCollegeChange() {
  majorId.value = ''; courseId.value = ''
  if (collegeId.value) { try { majors.value = await getMajors(collegeId.value) } catch { majors.value = [] } }
  else majors.value = []
  courses.value = []
  fetchBooks()
}
async function onMajorChange() {
  courseId.value = ''
  if (majorId.value) { try { courses.value = await getCourses(majorId.value) } catch { courses.value = [] } }
  else courses.value = []
  fetchBooks()
}
watch([collegeId, majorId, courseId], () => { fetchBooks() })

async function fetchBooks() {
  loading.value = true; error.value = ''
  try {
    const params = { page: page.value, per_page: 12 }
    if (keyword.value) params.keyword = keyword.value
    if (collegeId.value) params.college_id = collegeId.value
    if (majorId.value) params.major_id = majorId.value
    if (courseId.value) params.course_id = courseId.value
    const data = await getBookList(params)
    books.value = data.list
    meta.value = data.meta
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

function doSearch() {
  page.value = 1
  fetchBooks()
}

function onPageChange(p) {
  page.value = p
  fetchBooks()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

fetchColleges()
fetchBooks()
</script>

<style scoped>
.search-bar { display: flex; gap: 12px; margin-bottom: 20px; }
.search-bar input { flex: 1; height: 44px; padding: 0 16px; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 15px; }
.search-bar input:focus { outline: none; border-color: var(--color-amber); box-shadow: 0 0 0 3px rgba(199,145,92,0.15); }
.filter-bar { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
.filter-bar select { height: 38px; padding: 0 12px; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 14px; background: #fff; }
.result-count { margin-left: auto; font-size: 14px; color: var(--color-warm-gray); }
.book-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
@media (max-width: 1024px) { .book-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px) { .book-grid { grid-template-columns: repeat(2, 1fr); } }
.book-card {
  background: var(--color-warm-card); border-radius: var(--radius-md);
  overflow: hidden; cursor: pointer; border: 1px solid var(--color-border-light);
  transition: transform 0.2s, box-shadow 0.2s;
}
.book-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-hover); border-color: var(--color-amber); }
.cover-wrap { aspect-ratio: 3/4; overflow: hidden; background: var(--color-border-light); }
.cover-wrap img { width: 100%; height: 100%; object-fit: cover; }
.cover-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 48px; color: var(--color-warm-gray); background: var(--color-border-light); }
.card-info { padding: 14px; }
.book-title { font-size: 15px; font-weight: 600; margin-bottom: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.book-meta { font-size: 13px; color: var(--color-warm-gray); margin-bottom: 10px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.card-bottom { display: flex; align-items: center; justify-content: space-between; }
.price { color: var(--color-amber); font-size: 18px; font-weight: 700; }
</style>
```

---

### Task 5.2: 书籍详情页

**Files:**
- Create: `frontend/src/views/BookDetail.vue`

- [ ] **Step 1: 创建 src/views/BookDetail.vue**

```vue
<template>
  <div class="detail-page">
    <AppLoading v-if="loading" />
    <AppError v-else-if="error" :message="error" @retry="fetchDetail" />
    <div v-else-if="book" class="detail-content">
      <!-- 图片轮播 -->
      <div class="gallery">
        <img v-if="currentImage" :src="currentImage" :alt="book.title" @click="showLightbox = true" />
        <div v-else class="gallery-placeholder">{{ book.title[0] }}</div>
        <div class="thumb-strip" v-if="images.length > 1">
          <img v-for="(img, i) in images" :key="i" :src="img.path" :class="{ active: i === currentIdx }" @click="currentIdx = i" />
        </div>
      </div>

      <!-- 书籍信息 -->
      <div class="book-info">
        <h1>{{ book.title }}</h1>
        <div class="meta-row"><span>作者：{{ book.author }}</span><span>出版社：{{ book.publisher }}</span></div>
        <p v-if="book.isbn" class="isbn">ISBN：{{ book.isbn }}</p>
        <StatusBadge :status="book.condition" :label="book.condition_label" />
        <PriceTag :original="book.original_price" :current="book.price" class="price-row" />

        <div class="actions">
          <button class="btn-outline" @click="addToCart">加入购物车</button>
          <button class="btn-primary" @click="buyNow">立即购买</button>
        </div>
      </div>

      <!-- 评价 -->
      <div v-if="reviews.length" class="reviews">
        <h3>评价 ({{ reviews.length }}条) <RatingStar :rating="avgRating" :showScore="true" /></h3>
        <div v-for="r in reviews.slice(0, 3)" :key="r.id" class="review-item">
          <RatingStar :rating="r.book_rating" />
          <p>{{ r.comment }}</p>
          <span class="reviewer">— {{ r.user_name }}</span>
        </div>
      </div>
    </div>

    <!-- 灯箱 -->
    <Teleport to="body">
      <div v-if="showLightbox" class="lightbox" @click="showLightbox = false">
        <img :src="currentImage" />
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getBookDetail } from '@/api/books'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import AppLoading from '@/components/AppLoading.vue'
import AppError from '@/components/AppError.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import PriceTag from '@/components/PriceTag.vue'
import RatingStar from '@/components/RatingStar.vue'

const route = useRoute()
const router = useRouter()
const cart = useCartStore()
const auth = useAuthStore()

const book = ref(null)
const images = ref([])
const reviews = ref([])
const loading = ref(true)
const error = ref('')
const currentIdx = ref(0)
const showLightbox = ref(false)

const currentImage = computed(() => images.value[currentIdx.value]?.path || null)
const avgRating = computed(() => {
  if (!reviews.value.length) return 0
  return reviews.value.reduce((s, r) => s + r.book_rating, 0) / reviews.value.length
})

async function fetchDetail() {
  loading.value = true; error.value = ''
  try {
    const data = await getBookDetail(route.params.id)
    book.value = data.book
    images.value = data.images || []
    reviews.value = data.reviews || []
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

function addToCart() {
  if (!auth.isLoggedIn) return router.push('/login')
  cart.addItem(book.value)
}

function buyNow() {
  if (!auth.isLoggedIn) return router.push('/login')
  cart.addItem(book.value)
  router.push('/checkout')
}

fetchDetail()
</script>

<style scoped>
.detail-content { max-width: 1000px; margin: 0 auto; }
.gallery { margin-bottom: 32px; }
.gallery img { width: 100%; max-height: 500px; object-fit: contain; background: var(--color-border-light); border-radius: var(--radius-md); cursor: zoom-in; }
.gallery-placeholder { width: 100%; height: 400px; display: flex; align-items: center; justify-content: center; font-size: 72px; color: var(--color-warm-gray); background: var(--color-border-light); border-radius: var(--radius-md); }
.thumb-strip { display: flex; gap: 8px; margin-top: 12px; }
.thumb-strip img { width: 64px; height: 84px; object-fit: cover; border-radius: var(--radius-sm); cursor: pointer; border: 2px solid transparent; }
.thumb-strip img.active { border-color: var(--color-amber); }
.book-info h1 { font-size: 24px; margin-bottom: 12px; }
.meta-row { display: flex; gap: 24px; color: var(--color-warm-gray); font-size: 15px; margin-bottom: 8px; }
.isbn { color: var(--color-warm-gray); font-size: 14px; margin-bottom: 12px; }
.price-row { margin: 20px 0; }
.actions { display: flex; gap: 12px; margin-top: 24px; }
.actions .btn-primary, .actions .btn-outline { padding: 12px 40px; font-size: 16px; }
.reviews { margin-top: 40px; padding-top: 24px; border-top: 1px solid var(--color-border-light); }
.reviews h3 { font-size: 18px; margin-bottom: 16px; display: flex; align-items: center; gap: 12px; }
.review-item { margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border-light); }
.review-item p { margin: 8px 0; font-size: 15px; }
.reviewer { font-size: 13px; color: var(--color-warm-gray); }
.lightbox { position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.9); display: flex; align-items: center; justify-content: center; cursor: pointer; }
.lightbox img { max-width: 90vw; max-height: 90vh; }
</style>
```

---

## Phase 6: 购物车 + 下单

### Task 6.1: 购物车页

**Files:**
- Create: `frontend/src/views/Cart.vue`

- [ ] **Step 1: 创建 src/views/Cart.vue**

```vue
<template>
  <div class="cart-page">
    <h2>购物车（{{ cart.count }} 件）</h2>

    <AppEmpty v-if="cart.count === 0" text="购物车是空的" :link="{ name: 'Home' }" linkText="去逛逛" />

    <template v-else>
      <div class="cart-list">
        <div v-for="item in cart.items" :key="item.book_id" class="cart-item">
          <input type="checkbox" :checked="cart.checkedIds.includes(item.book_id)" @change="cart.toggleCheck(item.book_id)" />
          <div class="item-cover" @click="$router.push(`/books/${item.book_id}`)">
            <img v-if="item.cover_img" :src="item.cover_img" />
            <div v-else class="cover-ph">{{ item.title[0] }}</div>
          </div>
          <div class="item-info" @click="$router.push(`/books/${item.book_id}`)">
            <h4>{{ item.title }}</h4>
            <StatusBadge :status="item.condition" :label="item.condition_label" />
          </div>
          <span class="item-price">¥{{ item.price }}</span>
          <button class="btn-delete" @click="cart.removeItem(item.book_id)">删除</button>
        </div>
      </div>

      <div class="cart-footer">
        <label><input type="checkbox" @change="cart.checkAll" /> 全选</label>
        <span>已选 {{ cart.checkedItems.length }} 件，合计：<strong>¥{{ cart.totalAmount }}</strong></span>
        <button class="btn-primary" :disabled="cart.checkedItems.length === 0" @click="$router.push('/checkout')">去结算</button>
      </div>
    </template>
  </div>
</template>

<script setup>
import { useCartStore } from '@/stores/cart'
import AppEmpty from '@/components/AppEmpty.vue'
import StatusBadge from '@/components/StatusBadge.vue'

const cart = useCartStore()
</script>

<style scoped>
h2 { margin-bottom: 24px; }
.cart-list { display: flex; flex-direction: column; gap: 12px; }
.cart-item {
  display: flex; align-items: center; gap: 14px;
  background: var(--color-warm-card); border: 1px solid var(--color-border-light);
  border-radius: var(--radius-md); padding: 14px;
}
.item-cover { width: 60px; height: 80px; overflow: hidden; border-radius: var(--radius-sm); cursor: pointer; background: var(--color-border-light); flex-shrink: 0; }
.item-cover img { width: 100%; height: 100%; object-fit: cover; }
.cover-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--color-warm-gray); }
.item-info { flex: 1; cursor: pointer; }
.item-info h4 { font-size: 15px; margin-bottom: 6px; }
.item-price { color: var(--color-amber); font-size: 18px; font-weight: 700; }
.btn-delete { background: none; border: none; color: var(--color-danger); font-size: 14px; cursor: pointer; }
.cart-footer {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: 24px; padding: 18px 0; border-top: 2px solid var(--color-border);
}
.cart-footer strong { color: var(--color-amber); font-size: 20px; }
</style>
```

---

### Task 6.2: 确认下单页

**Files:**
- Create: `frontend/src/views/Checkout.vue`

- [ ] **Step 1: 创建 src/views/Checkout.vue**

```vue
<template>
  <div class="checkout-page">
    <h2>确认订单</h2>

    <!-- 取书地点 -->
    <div class="section">
      <h3>取书地点</h3>
      <div class="pickup-options">
        <label v-for="loc in presetLocations" :key="loc" class="pickup-option">
          <input type="radio" v-model="pickupLocation" :value="loc" />
          {{ loc }}
        </label>
        <label class="pickup-option">
          <input type="radio" v-model="pickupLocation" value="" />
          其他：<input type="text" v-model="customLocation" placeholder="请填写取书地点" class="custom-input" />
        </label>
      </div>
    </div>

    <!-- 商品明细 -->
    <div class="section">
      <h3>商品明细</h3>
      <div class="items-list">
        <div v-for="item in cart.checkedItems" :key="item.book_id" class="item-row">
          <span>{{ item.title }}</span>
          <span class="item-price">¥{{ item.price }}</span>
        </div>
      </div>
      <div class="total-line">
        <span>共 {{ cart.checkedItems.length }} 件</span>
        <span>合计：<strong>¥{{ cart.totalAmount }}</strong></span>
      </div>
    </div>

    <div class="checkout-actions">
      <router-link to="/cart" class="btn-outline">返回购物车</router-link>
      <button class="btn-primary" :disabled="submitting || !finalLocation" @click="submitOrder">
        {{ submitting ? '提交中...' : '提交订单' }}
      </button>
    </div>

    <div v-if="submitError" class="alert-error">{{ submitError }}</div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { createOrder } from '@/api/orders'

const router = useRouter()
const cart = useCartStore()

const presetLocations = ['图书馆一楼大厅', '教学楼A栋门口', '食堂西侧']
const pickupLocation = ref(presetLocations[0])
const customLocation = ref('')
const submitting = ref(false)
const submitError = ref('')

const finalLocation = computed(() => pickupLocation.value || customLocation.value)

async function submitOrder() {
  if (!finalLocation.value) return
  submitting.value = true; submitError.value = ''
  try {
    const data = await createOrder({
      book_ids: cart.checkedItems.map(i => i.book_id),
      pickup_location: finalLocation.value
    })
    cart.clearChecked()
    router.push(`/orders/${data.order.id}`)
  } catch (e) {
    submitError.value = e.message
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
h2 { margin-bottom: 24px; }
.section { margin-bottom: 28px; }
.section h3 { font-size: 16px; margin-bottom: 12px; }
.pickup-options { display: flex; flex-direction: column; gap: 10px; }
.pickup-option { font-size: 15px; cursor: pointer; }
.custom-input { height: 34px; padding: 0 10px; border: 1px solid var(--color-border); border-radius: var(--radius-sm); font-size: 14px; margin-left: 8px; width: 200px; }
.items-list { display: flex; flex-direction: column; gap: 10px; }
.item-row { display: flex; justify-content: space-between; font-size: 15px; }
.item-price { color: var(--color-amber); font-weight: 600; }
.total-line { display: flex; justify-content: space-between; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--color-border); font-size: 16px; }
.total-line strong { color: var(--color-amber); font-size: 22px; }
.checkout-actions { display: flex; justify-content: space-between; margin-top: 32px; }
.alert-error { margin-top: 16px; background: #fee2e2; color: var(--color-danger); padding: 10px 14px; border-radius: var(--radius-md); font-size: 14px; }
</style>
```

---

## Phase 7: 订单

### Task 7.1: 我的订单列表

**Files:**
- Create: `frontend/src/views/Orders.vue`

- [ ] **Step 1: 创建 src/views/Orders.vue**

```vue
<template>
  <div class="orders-page">
    <h2>我的订单</h2>

    <div class="status-tabs">
      <button v-for="t in tabs" :key="t.value" :class="{ active: currentTab === t.value }" @click="switchTab(t.value)">{{ t.label }}</button>
    </div>

    <AppLoading v-if="loading" />
    <AppError v-else-if="error" :message="error" @retry="fetchOrders" />
    <AppEmpty v-else-if="orders.length === 0" text="暂无订单" :link="{ name: 'Home' }" linkText="去逛逛" />

    <div v-else class="order-list">
      <div v-for="order in orders" :key="order.id" class="order-card" @click="$router.push(`/orders/${order.id}`)">
        <div class="order-header">
          <span class="order-no">{{ order.order_no }}</span>
          <StatusBadge :status="order.status" :label="order.status_label" />
        </div>
        <div v-for="item in order.items" :key="item.id" class="order-item">
          <span>{{ item.book_title }}</span>
          <span>¥{{ item.price }}</span>
        </div>
        <div class="order-footer">
          <span>共 {{ order.items.length }} 件 · 合计：<strong>¥{{ order.total_amount }}</strong></span>
          <span class="order-time">{{ order.created_at }}</span>
        </div>
      </div>

      <Pagination :current="meta.current_page" :last="meta.last_page" :total="meta.total" @change="onPageChange" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { getOrderList } from '@/api/orders'
import AppLoading from '@/components/AppLoading.vue'
import AppError from '@/components/AppError.vue'
import AppEmpty from '@/components/AppEmpty.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Pagination from '@/components/Pagination.vue'

const tabs = [
  { label: '全部', value: '' }, { label: '待付款', value: 'pending' },
  { label: '已付款', value: 'paid' }, { label: '已确认', value: 'confirmed' },
  { label: '已取书', value: 'picked_up' }, { label: '已完成', value: 'completed' }
]

const currentTab = ref('')
const orders = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const loading = ref(true)
const error = ref('')
const page = ref(1)

async function fetchOrders() {
  loading.value = true; error.value = ''
  try {
    const params = { page: page.value }
    if (currentTab.value) params.status = currentTab.value
    const data = await getOrderList(params)
    orders.value = data.list
    meta.value = data.meta
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

function switchTab(tab) { currentTab.value = tab; page.value = 1; fetchOrders() }
function onPageChange(p) { page.value = p; fetchOrders() }

fetchOrders()
</script>

<style scoped>
h2 { margin-bottom: 20px; }
.status-tabs { display: flex; gap: 0; margin-bottom: 20px; }
.status-tabs button {
  padding: 8px 20px; border: 1px solid var(--color-border); background: #fff;
  font-size: 14px; cursor: pointer; color: var(--color-ink);
}
.status-tabs button:first-child { border-radius: var(--radius-md) 0 0 var(--radius-md); }
.status-tabs button:last-child { border-radius: 0 var(--radius-md) var(--radius-md) 0; }
.status-tabs button.active { background: var(--color-amber); color: #fff; border-color: var(--color-amber); }
.order-list { display: flex; flex-direction: column; gap: 16px; }
.order-card { background: var(--color-warm-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-md); padding: 18px; cursor: pointer; transition: box-shadow 0.2s; }
.order-card:hover { box-shadow: var(--shadow-md); }
.order-header { display: flex; justify-content: space-between; margin-bottom: 12px; }
.order-no { font-size: 14px; color: var(--color-warm-gray); }
.order-item { display: flex; justify-content: space-between; font-size: 15px; padding: 4px 0; }
.order-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--color-border-light); font-size: 14px; }
.order-footer strong { color: var(--color-amber); font-size: 18px; }
.order-time { color: var(--color-warm-gray); }
</style>
```

---

### Task 7.2: 订单详情页

**Files:**
- Create: `frontend/src/views/OrderDetail.vue`

- [ ] **Step 1: 创建 src/views/OrderDetail.vue**

```vue
<template>
  <div class="order-detail-page">
    <AppLoading v-if="loading" />
    <AppError v-else-if="error" :message="error" @retry="fetchDetail" />
    <template v-else-if="order">
      <div class="detail-header">
        <router-link to="/orders" class="back-link">&larr; 返回</router-link>
        <h2>订单详情</h2>
      </div>

      <div class="detail-card">
        <div class="card-header">
          <span>{{ order.order_no }}</span>
          <StatusBadge :status="order.status" :label="order.status_label" />
        </div>

        <!-- 进度条 -->
        <div class="progress-bar">
          <div v-for="(s, i) in steps" :key="s.value" class="step" :class="{ done: i <= currentStep, cancelled: order.status === 'cancelled' && i >= currentStep }">
            <div class="dot"></div>
            <span>{{ s.label }}</span>
          </div>
        </div>

        <!-- 商品 -->
        <div class="items">
          <div v-for="item in order.items" :key="item.id" class="item-row">
            <span>{{ item.book_title }}</span>
            <span>¥{{ item.price }}</span>
          </div>
          <div class="total">合计：<strong>¥{{ order.total_amount }}</strong></div>
        </div>

        <!-- 取书 -->
        <div class="info-row"><label>取书地点：</label>{{ order.pickup_location }}</div>

        <!-- 卖家信息（购买后可见） -->
        <div v-if="order.seller && showSeller" class="info-row">
          <label>卖家：</label>{{ order.seller.name }} {{ order.seller.phone }}
        </div>

        <!-- 时间线 -->
        <div class="timeline">
          <div v-for="t in order.timeline" :key="t.id" class="tl-item">
            <span class="tl-time">{{ t.created_at }}</span>
            <span>{{ t.event }}</span>
          </div>
        </div>

        <!-- 操作按钮 -->
        <div v-if="actions.length" class="actions">
          <button v-for="a in actions" :key="a.key" :class="a.cls" @click="a.handler">{{ a.label }}</button>
        </div>
      </div>

      <!-- 评价弹窗 -->
      <ConfirmModal v-if="showReview" :visible="showReview" text="请评价本次交易" confirmText="提交评价" cancelText="稍后" @confirm="submitReview" @cancel="showReview = false">
        <template #default>
          <div class="review-form">
            <div><label>书况评分</label><RatingStar :rating="reviewForm.book_rating" @rate="reviewForm.book_rating = $event" /></div>
            <div><label>服务评分</label><RatingStar :rating="reviewForm.service_rating" @rate="reviewForm.service_rating = $event" /></div>
            <textarea v-model="reviewForm.comment" placeholder="写下你的评价（选填）" maxlength="500"></textarea>
          </div>
        </template>
      </ConfirmModal>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { getOrderDetail, payOrder, cancelOrder, pickupOrder, reviewOrder } from '@/api/orders'
import AppLoading from '@/components/AppLoading.vue'
import AppError from '@/components/AppError.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import RatingStar from '@/components/RatingStar.vue'
import ConfirmModal from '@/components/ConfirmModal.vue'

const route = useRoute()
const order = ref(null)
const loading = ref(true)
const error = ref('')
const showReview = ref(false)
const reviewForm = ref({ book_rating: 5, service_rating: 5, comment: '' })

const steps = [
  { value: 'pending', label: '待付款' }, { value: 'paid', label: '已付款' },
  { value: 'confirmed', label: '已确认' }, { value: 'picked_up', label: '已取书' },
  { value: 'completed', label: '已完成' }
]
const stepValues = steps.map(s => s.value)
const currentStep = computed(() => stepValues.indexOf(order.value?.status))
const showSeller = computed(() => order.value && !['pending', 'cancelled'].includes(order.value.status))

const actions = computed(() => {
  if (!order.value) return []
  const s = order.value.status
  const acts = []
  if (s === 'pending') acts.push({ key: 'pay', label: '去支付', cls: 'btn-primary', handler: doPay }, { key: 'cancel', label: '取消订单', cls: 'btn-outline', handler: doCancel })
  if (s === 'paid') acts.push({ key: 'cancel', label: '取消订单', cls: 'btn-outline', handler: doCancel })
  if (s === 'confirmed') acts.push({ key: 'pickup', label: '确认取书', cls: 'btn-primary', handler: doPickup })
  if (s === 'picked_up') acts.push({ key: 'review', label: '去评价', cls: 'btn-primary', handler: () => { showReview.value = true } })
  return acts
})

async function fetchDetail() {
  loading.value = true; error.value = ''
  try {
    order.value = (await getOrderDetail(route.params.id)).order
  } catch (e) {
    error.value = e.message
  } finally {
    loading.value = false
  }
}

async function doPay() { try { await payOrder(order.value.id); fetchDetail() } catch (e) { alert(e.message) } }
async function doCancel() { if (!confirm('确定取消订单？')) return; try { await cancelOrder(order.value.id); fetchDetail() } catch (e) { alert(e.message) } }
async function doPickup() { if (!confirm('确认已取到书？')) return; try { await pickupOrder(order.value.id); fetchDetail() } catch (e) { alert(e.message) } }
async function submitReview() {
  try { await reviewOrder(order.value.id, reviewForm.value); showReview.value = false; fetchDetail() } catch (e) { alert(e.message) }
}

fetchDetail()
</script>

<style scoped>
.detail-header { margin-bottom: 20px; }
.back-link { color: var(--color-amber); font-size: 14px; }
h2 { margin-top: 8px; }
.detail-card { background: var(--color-warm-card); border-radius: var(--radius-md); padding: 28px; border: 1px solid var(--color-border-light); }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 15px; }
.progress-bar { display: flex; justify-content: space-between; margin-bottom: 28px; position: relative; }
.step { display: flex; flex-direction: column; align-items: center; gap: 6px; font-size: 13px; color: var(--color-warm-gray); flex: 1; }
.step .dot { width: 14px; height: 14px; border-radius: 50%; background: var(--color-border); }
.step.done .dot { background: var(--color-amber); }
.step.cancelled .dot { background: var(--color-danger); }
.items { margin-bottom: 20px; }
.item-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 15px; }
.total { text-align: right; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--color-border-light); }
.total strong { color: var(--color-amber); font-size: 20px; }
.info-row { margin-bottom: 10px; font-size: 15px; }
.info-row label { color: var(--color-warm-gray); }
.timeline { margin: 20px 0; padding: 16px; background: #f9fafb; border-radius: var(--radius-md); }
.tl-item { display: flex; gap: 16px; padding: 6px 0; font-size: 14px; }
.tl-time { color: var(--color-warm-gray); white-space: nowrap; }
.actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; }
</style>
```

---

## Phase 8: 卖书模块

### Task 8.1: 我的卖书列表

**Files:**
- Create: `frontend/src/views/MyBooks.vue`

- [ ] **Step 1: 创建 src/views/MyBooks.vue**

```vue
<template>
  <div class="my-books-page">
    <h2>我的卖书</h2>
    <router-link to="/submit-book" class="btn-primary" style="float:right">提交卖书</router-link>

    <div class="status-tabs">
      <button v-for="t in tabs" :key="t.value" :class="{ active: currentTab === t.value }" @click="switchTab(t.value)">{{ t.label }}</button>
    </div>

    <AppLoading v-if="loading" />
    <AppError v-else-if="error" :message="error" @retry="fetchBooks" />
    <AppEmpty v-else-if="books.length === 0" text="暂无卖书记录" :link="{ name: 'SubmitBook' }" linkText="去卖书" />

    <div v-else class="book-list">
      <div v-for="b in books" :key="b.id" class="book-card">
        <div class="cover">
          <img v-if="b.cover_img" :src="b.cover_img" />
          <div v-else class="cover-ph">{{ b.title[0] }}</div>
        </div>
        <div class="info">
          <h4>{{ b.title }}</h4>
          <StatusBadge :status="b.status" :label="b.status_label" />
          <p v-if="b.price">售价：¥{{ b.price }} <span v-if="b.cost_price" style="color:var(--color-warm-gray);margin-left:8px">收书价：¥{{ b.cost_price }}</span></p>
          <p v-if="b.reject_reason" class="reject-reason">驳回原因：{{ b.reject_reason }}</p>
          <p class="time">提交时间：{{ b.submitted_at }}</p>
        </div>
      </div>

      <Pagination :current="meta.current_page" :last="meta.last_page" :total="meta.total" @change="onPageChange" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { getMyBooks } from '@/api/books'
import AppLoading from '@/components/AppLoading.vue'
import AppError from '@/components/AppError.vue'
import AppEmpty from '@/components/AppEmpty.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Pagination from '@/components/Pagination.vue'

const tabs = [
  { label: '全部', value: '' }, { label: '待审核', value: 'pending_review' },
  { label: '已通过', value: 'approved' }, { label: '在售', value: 'active' },
  { label: '已售出', value: 'sold' }, { label: '已驳回', value: 'removed' }
]

const currentTab = ref('')
const books = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const loading = ref(true)
const error = ref('')
const page = ref(1)

async function fetchBooks() {
  loading.value = true; error.value = ''
  try {
    const params = { page: page.value }
    if (currentTab.value) params.status = currentTab.value
    const data = await getMyBooks(params)
    books.value = data.list
    meta.value = data.meta
  } catch (e) { error.value = e.message } finally { loading.value = false }
}

function switchTab(tab) { currentTab.value = tab; page.value = 1; fetchBooks() }
function onPageChange(p) { page.value = p; fetchBooks() }
fetchBooks()
</script>

<style scoped>
.status-tabs { display: flex; gap: 0; margin: 20px 0; clear: both; }
.status-tabs button {
  padding: 8px 20px; border: 1px solid var(--color-border); background: #fff;
  font-size: 14px; cursor: pointer;
}
.status-tabs button:first-child { border-radius: var(--radius-md) 0 0 var(--radius-md); }
.status-tabs button:last-child { border-radius: 0 var(--radius-md) var(--radius-md) 0; }
.status-tabs button.active { background: var(--color-amber); color: #fff; border-color: var(--color-amber); }
.book-card { display: flex; gap: 16px; background: var(--color-warm-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-md); padding: 16px; margin-bottom: 12px; }
.cover { width: 80px; height: 104px; overflow: hidden; border-radius: var(--radius-sm); background: var(--color-border-light); flex-shrink: 0; }
.cover img { width: 100%; height: 100%; object-fit: cover; }
.cover-ph { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 28px; color: var(--color-warm-gray); }
.info { flex: 1; }
.info h4 { font-size: 16px; margin-bottom: 6px; }
.reject-reason { color: var(--color-danger); font-size: 14px; }
.time { color: var(--color-warm-gray); font-size: 13px; margin-top: 4px; }
</style>
```

---

### Task 8.2: 提交卖书页

**Files:**
- Create: `frontend/src/views/SubmitBook.vue`

- [ ] **Step 1: 创建 src/views/SubmitBook.vue**

```vue
<template>
  <div class="submit-page">
    <router-link to="/my-books" class="back-link">&larr; 返回</router-link>
    <h2>提交卖书</h2>

    <!-- 图片上传 -->
    <div class="section">
      <h3>上传图片（至少3张，最多9张）</h3>
      <div class="image-uploader">
        <div v-for="(img, i) in images" :key="i" class="preview-item">
          <img :src="img.preview" />
          <button class="remove-btn" @click="removeImage(i)">×</button>
          <select v-model="img.type" class="type-select">
            <option value="cover">封面</option>
            <option value="inner">内页</option>
            <option value="spine">书脊</option>
            <option value="other">其他</option>
          </select>
        </div>
        <div v-if="images.length < 9" class="upload-btn" @click="triggerUpload">
          <input ref="fileInput" type="file" accept="image/jpeg,image/png,image/webp" multiple @change="onFileChange" hidden />
          <span>+</span>
          <span>上传</span>
        </div>
      </div>
      <p v-if="imgError" class="field-error">{{ imgError }}</p>
    </div>

    <!-- 表单 -->
    <form @submit.prevent="handleSubmit">
      <div class="form-row">
        <div class="form-group"><label>书名 *</label><input v-model="form.title" placeholder="请输入书名" /></div>
        <div class="form-group"><label>作者 *</label><input v-model="form.author" placeholder="请输入作者" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>出版社 *</label><input v-model="form.publisher" placeholder="请输入出版社" /></div>
        <div class="form-group"><label>ISBN</label><input v-model="form.isbn" placeholder="选填" /></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>分类 *</label><select v-model="form.category_id"><option value="">请选择</option><option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option></select></div>
        <div class="form-group"><label>成色 *</label><select v-model="form.condition"><option value="">请选择</option><option value="like_new">全新</option><option value="excellent">几乎全新</option><option value="good">正常使用</option><option value="fair">较旧</option></select></div>
      </div>
      <div class="form-group"><label>原价 *</label><input v-model="form.original_price" type="number" step="0.01" placeholder="¥" /></div>
      <div class="form-group"><label>补充说明</label><textarea v-model="form.description" placeholder="选填，最多500字" maxlength="500"></textarea></div>

      <p v-if="submitError" class="alert-error">{{ submitError }}</p>
      <button type="submit" class="btn-primary btn-lg" :disabled="submitting">{{ submitting ? '提交中...' : '提交审核' }}</button>
      <p class="hint">提交后平台会在1-2个工作日内审核</p>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { submitBook } from '@/api/books'
import { getCategories } from '@/api/categories'

const router = useRouter()
const images = ref([])
const fileInput = ref(null)
const imgError = ref('')
const categories = ref([])
const form = reactive({
  title: '', author: '', publisher: '', isbn: '', category_id: '', condition: '',
  original_price: '', description: ''
})
const submitting = ref(false)
const submitError = ref('')

function triggerUpload() { fileInput.value.click() }

function onFileChange(e) {
  const files = Array.from(e.target.files)
  for (const f of files) {
    if (images.value.length >= 9) break
    if (f.size > 5 * 1024 * 1024) { imgError.value = '单张图片不能超过5MB'; return }
    images.value.push({ file: f, preview: URL.createObjectURL(f), type: 'cover' })
  }
  imgError.value = ''
}

function removeImage(i) { images.value.splice(i, 1) }

async function handleSubmit() {
  if (images.value.length < 3) { imgError.value = '请至少上传3张图片'; return }
  submitError.value = ''
  submitting.value = true
  try {
    const fd = new FormData()
    Object.entries(form).forEach(([k, v]) => fd.append(k, v))
    images.value.forEach((img, i) => {
      fd.append(`images[${i}]`, img.file)
      fd.append(`image_types[${i}]`, img.type)
    })
    await submitBook(fd)
    router.push('/my-books')
  } catch (e) {
    submitError.value = e.message
  } finally {
    submitting.value = false
  }
}

onMounted(async () => { try { categories.value = await getCategories() } catch {} })
</script>

<style scoped>
.back-link { color: var(--color-amber); font-size: 14px; }
h2 { margin: 8px 0 24px; }
.section { margin-bottom: 28px; }
.section h3 { font-size: 16px; margin-bottom: 12px; }
.image-uploader { display: flex; flex-wrap: wrap; gap: 12px; }
.preview-item { position: relative; width: 120px; height: 156px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--color-border); }
.preview-item img { width: 100%; height: 100%; object-fit: cover; }
.remove-btn { position: absolute; top: 4px; right: 4px; width: 22px; height: 22px; border-radius: 50%; background: rgba(0,0,0,0.6); color: #fff; border: none; cursor: pointer; font-size: 14px; }
.type-select { position: absolute; bottom: 4px; left: 4px; right: 4px; font-size: 11px; height: 24px; border: none; background: rgba(0,0,0,0.5); color: #fff; border-radius: 2px; }
.upload-btn { width: 120px; height: 156px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px dashed var(--color-border); border-radius: var(--radius-sm); cursor: pointer; color: var(--color-warm-gray); font-size: 24px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: var(--color-ink); }
.form-group input, .form-group select, .form-group textarea {
  width: 100%; height: 42px; padding: 0 14px;
  border: 1px solid var(--color-border); border-radius: var(--radius-md);
  font-size: 15px; background: #fff;
}
.form-group textarea { height: 100px; padding: 12px 14px; resize: vertical; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: var(--color-amber); }
.field-error { color: var(--color-danger); font-size: 13px; margin-top: 4px; }
.alert-error { background: #fee2e2; color: var(--color-danger); padding: 10px 14px; border-radius: var(--radius-md); margin-bottom: 16px; font-size: 14px; }
.btn-lg { padding: 14px 48px; font-size: 16px; }
.hint { margin-top: 12px; font-size: 13px; color: var(--color-warm-gray); }
</style>
```

---

## Phase 9: 求购模块

### Task 9.1: 求购广场 + 详情 + 发布

**Files:**
- Create: `frontend/src/views/Wants.vue`
- Create: `frontend/src/views/WantDetail.vue`
- Create: `frontend/src/views/PostWant.vue`

- [ ] **Step 1: 创建 src/views/Wants.vue**

```vue
<template>
  <div class="wants-page">
    <h2>求购广场</h2>
    <router-link to="/post-want" class="btn-primary" style="float:right">发布求购</router-link>

    <div class="search-bar" style="clear:both; margin-bottom:20px">
      <input v-model="keyword" placeholder="搜索求购书名..." @keyup.enter="fetchWants" />
      <button class="btn-primary" @click="fetchWants">搜索</button>
    </div>

    <AppLoading v-if="loading" />
    <AppError v-else-if="error" :message="error" @retry="fetchWants" />
    <AppEmpty v-else-if="wants.length === 0" text="暂无求购" />

    <div v-else class="want-list">
      <div v-for="w in wants" :key="w.id" class="want-card" @click="$router.push(`/wants/${w.id}`)">
        <div class="want-header">
          <h4>求购：{{ w.title }}</h4>
          <StatusBadge :status="w.status === 'active' ? 'active_want' : w.status" :label="w.status_label" />
        </div>
        <p v-if="w.author">作者：{{ w.author }}</p>
        <p>最高接受价：¥{{ w.max_price }} · {{ w.acceptable_condition }}</p>
        <p class="want-meta">{{ w.fulfiller_count }}人接单 · {{ w.remaining_days }}</p>
      </div>

      <Pagination :current="meta.current_page" :last="meta.last_page" :total="meta.total" @change="onPageChange" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { getWantList } from '@/api/wants'
import AppLoading from '@/components/AppLoading.vue'
import AppError from '@/components/AppError.vue'
import AppEmpty from '@/components/AppEmpty.vue'
import StatusBadge from '@/components/StatusBadge.vue'
import Pagination from '@/components/Pagination.vue'

const keyword = ref('')
const wants = ref([])
const meta = ref({ current_page: 1, last_page: 1, total: 0 })
const loading = ref(true)
const error = ref('')
const page = ref(1)

async function fetchWants() {
  loading.value = true; error.value = ''
  try {
    const params = { page: page.value }
    if (keyword.value) params.keyword = keyword.value
    const data = await getWantList(params)
    wants.value = data.list
    meta.value = data.meta
  } catch (e) { error.value = e.message } finally { loading.value = false }
}

function onPageChange(p) { page.value = p; fetchWants() }
fetchWants()
</script>

<style scoped>
.want-card { background: var(--color-warm-card); border: 1px solid var(--color-border-light); border-radius: var(--radius-md); padding: 18px; margin-bottom: 12px; cursor: pointer; transition: box-shadow 0.2s; }
.want-card:hover { box-shadow: var(--shadow-md); }
.want-header { display: flex; justify-content: space-between; margin-bottom: 8px; }
.want-header h4 { font-size: 16px; }
.want-meta { color: var(--color-warm-gray); font-size: 13px; margin-top: 4px; }
.search-bar { display: flex; gap: 12px; margin: 20px 0; }
.search-bar input { flex: 1; height: 42px; padding: 0 14px; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 15px; }
.search-bar input:focus { outline: none; border-color: var(--color-amber); }
</style>
```

- [ ] **Step 2: 创建 src/views/WantDetail.vue**

```vue
<template>
  <div class="want-detail-page">
    <AppLoading v-if="loading" />
    <AppError v-else-if="error" :message="error" @retry="fetchDetail" />
    <template v-else-if="want">
      <router-link to="/wants" class="back-link">&larr; 返回</router-link>
      <div class="detail-card">
        <div class="card-header">
          <h2>求购：{{ want.title }}</h2>
          <StatusBadge :status="want.status === 'active' ? 'active_want' : want.status" :label="want.status_label" />
        </div>
        <div class="info-grid">
          <p v-if="want.author"><label>作者：</label>{{ want.author }}</p>
          <p v-if="want.publisher"><label>出版社：</label>{{ want.publisher }}</p>
          <p><label>最高接受价：</label>¥{{ want.max_price }}</p>
          <p><label>可接受成色：</label>{{ want.acceptable_condition }}</p>
          <p><label>发布时间：</label>{{ want.created_at }} · {{ want.remaining_days }}</p>
        </div>

        <div v-if="canFulfill" class="actions">
          <button v-if="!hasFulfilled" class="btn-primary" @click="doFulfill">我有这本书，我可以卖</button>
          <p v-else class="hint">您已接单</p>
        </div>

        <div v-if="want.fulfillments?.length" class="fulfillments">
          <h3>接单记录（{{ want.fulfillments.length }}人）</h3>
          <div v-for="f in want.fulfillments" :key="f.id" class="f-item">
            {{ f.user_name }} · {{ f.created_at }} · {{ f.status_label }}
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getWantDetail, fulfillWant } from '@/api/wants'
import AppLoading from '@/components/AppLoading.vue'
import AppError from '@/components/AppError.vue'
import StatusBadge from '@/components/StatusBadge.vue'

const route = useRoute()
const auth = useAuthStore()
const want = ref(null)
const loading = ref(true)
const error = ref('')

const canFulfill = computed(() => auth.isLoggedIn && want.value?.status === 'active')
const hasFulfilled = ref(false)

async function fetchDetail() {
  loading.value = true; error.value = ''
  try { want.value = (await getWantDetail(route.params.id)).want } catch (e) { error.value = e.message } finally { loading.value = false }
}

async function doFulfill() {
  try {
    await fulfillWant(want.value.id)
    hasFulfilled.value = true
    fetchDetail()
  } catch (e) { alert(e.message) }
}

fetchDetail()
</script>

<style scoped>
.back-link { color: var(--color-amber); font-size: 14px; }
.detail-card { background: var(--color-warm-card); border-radius: var(--radius-md); padding: 28px; border: 1px solid var(--color-border-light); margin-top: 12px; }
.card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.info-grid p { margin-bottom: 10px; font-size: 15px; }
.info-grid label { color: var(--color-warm-gray); }
.actions { margin: 24px 0; }
.hint { color: var(--color-warm-gray); }
.fulfillments { margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--color-border); }
.fulfillments h3 { margin-bottom: 12px; }
.f-item { padding: 6px 0; font-size: 14px; }
</style>
```

- [ ] **Step 3: 创建 src/views/PostWant.vue**

```vue
<template>
  <div class="post-want-page">
    <router-link to="/wants" class="back-link">&larr; 返回</router-link>
    <h2>发布求购</h2>

    <form @submit.prevent="handleSubmit" class="want-form">
      <div class="form-group"><label>书名 *</label><input v-model="form.title" placeholder="请输入书名" /></div>
      <div class="form-row">
        <div class="form-group"><label>作者</label><input v-model="form.author" placeholder="选填" /></div>
        <div class="form-group"><label>出版社</label><input v-model="form.publisher" placeholder="选填" /></div>
      </div>
      <div class="form-group"><label>最高接受价 *</label><input v-model="form.max_price" type="number" step="0.01" placeholder="¥" /></div>
      <div class="form-group">
        <label>可接受成色（可多选，至少选一项）</label>
        <div class="check-group">
          <label v-for="c in conditions" :key="c.value"><input type="checkbox" :value="c.value" v-model="form.acceptable_conditions" /> {{ c.label }}</label>
        </div>
      </div>

      <p v-if="submitError" class="alert-error">{{ submitError }}</p>
      <button type="submit" class="btn-primary btn-lg" :disabled="submitting">{{ submitting ? '发布中...' : '发布求购' }}</button>
      <p class="hint">发布后有效期7天，到期自动过期</p>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { createWant } from '@/api/wants'

const router = useRouter()
const conditions = [
  { value: 'like_new', label: '全新' }, { value: 'excellent', label: '几乎全新' },
  { value: 'good', label: '正常使用' }, { value: 'fair', label: '较旧' }
]

const form = reactive({ title: '', author: '', publisher: '', max_price: '', acceptable_conditions: [] })
const submitting = ref(false)
const submitError = ref('')

async function handleSubmit() {
  if (form.acceptable_conditions.length === 0) { submitError.value = '请至少选一项可接受成色'; return }
  submitting.value = true; submitError.value = ''
  try {
    await createWant({
      ...form,
      acceptable_condition: form.acceptable_conditions.join(',')
    })
    router.push('/wants')
  } catch (e) { submitError.value = e.message } finally { submitting.value = false }
}
</script>

<style scoped>
.back-link { color: var(--color-amber); font-size: 14px; }
h2 { margin: 8px 0 24px; }
.want-form { max-width: 600px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 14px; font-weight: 500; margin-bottom: 6px; color: var(--color-ink); }
.form-group input { width: 100%; height: 42px; padding: 0 14px; border: 1px solid var(--color-border); border-radius: var(--radius-md); font-size: 15px; }
.form-group input:focus { outline: none; border-color: var(--color-amber); }
.check-group { display: flex; gap: 16px; flex-wrap: wrap; }
.check-group label { font-weight: 400; cursor: pointer; }
.alert-error { background: #fee2e2; color: var(--color-danger); padding: 10px 14px; border-radius: var(--radius-md); margin-bottom: 16px; font-size: 14px; }
.btn-lg { padding: 14px 48px; font-size: 16px; }
.hint { margin-top: 12px; font-size: 13px; color: var(--color-warm-gray); }
</style>
```

---

## Phase 10: 个人中心

### Task 10.1: 个人中心页

**Files:**
- Create: `frontend/src/views/Profile.vue`

- [ ] **Step 1: 创建 src/views/Profile.vue**

```vue
<template>
  <div class="profile-page">
    <div class="profile-card">
      <div class="user-info">
        <div class="avatar">{{ user?.name?.[0] || 'U' }}</div>
        <div>
          <h3>{{ user?.name }}</h3>
          <p>学号：{{ user?.student_id }}</p>
          <p>手机号：{{ user?.phone }}</p>
        </div>
      </div>
    </div>

    <div class="menu-list">
      <router-link to="/orders" class="menu-item">我的订单 <span>&gt;</span></router-link>
      <router-link to="/my-books" class="menu-item">我的卖书 <span>&gt;</span></router-link>
      <router-link to="/cart" class="menu-item">购物车 <span>&gt;</span></router-link>
    </div>

    <button class="btn-outline logout-btn" @click="handleLogout">退出登录</button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const user = computed(() => auth.user)

async function handleLogout() {
  await auth.logoutAction()
  router.push('/')
}
</script>

<style scoped>
.profile-card { background: var(--color-warm-card); border-radius: var(--radius-md); padding: 28px; border: 1px solid var(--color-border-light); margin-bottom: 20px; }
.user-info { display: flex; align-items: center; gap: 20px; }
.avatar { width: 64px; height: 64px; border-radius: 50%; background: var(--color-deep-blue); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 28px; }
.user-info h3 { font-size: 20px; margin-bottom: 4px; }
.user-info p { font-size: 14px; color: var(--color-warm-gray); }
.menu-list { background: var(--color-warm-card); border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--color-border-light); margin-bottom: 20px; }
.menu-item { display: flex; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--color-border-light); color: var(--color-ink); font-size: 15px; transition: background 0.2s; }
.menu-item:last-child { border-bottom: none; }
.menu-item:hover { background: var(--color-border-light); }
.logout-btn { width: 100%; padding: 12px; font-size: 15px; }
</style>
```

---

## Phase 11: 集成与收尾

### Task 11.1: 配置 Laravel 路由支持 SPA fallback

**Files:**
- Modify: `backend/routes/web.php`

- [ ] **Step 1: 在 web.php 底部添加 SPA catch-all 路由**

在 `backend/routes/web.php` 的最后添加：

```php
// SPA fallback：所有非 API/非 Admin/非静态资源的 GET 请求交给 Vue SPA
// 仅在 Blade 路由未匹配时生效
Route::get('/{any}', function () {
    return file_get_contents(public_path('../frontend/dist/index.html'));
})->where('any', '^(?!api|admin|storage).*');
```

> 注意：开发阶段 Vite dev server 在 localhost:3000 独立运行，通过 proxy 转发 API。此路由仅在生产构建后使用。

---

### Task 11.2: 逐步替换 Blade 视图路由

在 `backend/routes/web.php` 中，每完成一个 Vue 模块就注释掉对应的 Blade 路由：

```php
// Phase 4 完成后注释：
// Route::get('/login', 'Web\AuthController@loginForm');
// Route::post('/login', 'Web\AuthController@login');
// Route::get('/register', 'Web\AuthController@registerForm');
// Route::post('/register', 'Web\AuthController@register');

// Phase 5 完成后注释：
// Route::get('/', 'Web\HomeController@index');
// Route::get('/search', 'Web\HomeController@search');
// Route::get('/books/{id}', 'Web\HomeController@detail');

// Phase 7 完成后注释：
// Route::get('/orders', 'Web\OrderController@index');
// Route::get('/orders/{id}', 'Web\OrderController@detail');
// Route::get('/buy/{bookId}', 'Web\OrderController@buyForm');
// Route::post('/buy/{bookId}', 'Web\OrderController@buy');

// Phase 8 完成后注释：
// Route::get('/sell', 'Web\HomeController@sell');
// Route::post('/sell', 'Web\HomeController@postSell');
// Route::get('/my-sells', 'Web\HomeController@mySells');
```

---

## 开发环境运行方式

```bash
# 终端1：启动 Laravel 后端
cd backend && php artisan serve --host=127.0.0.1 --port=8000

# 终端2：启动 Vue dev server
cd frontend && npm run dev
# 访问 http://localhost:3000
# API 请求自动 proxy 到 :8000
```

## 开发顺序

| 顺序 | Phase | 说明 | 依赖 |
|------|-------|------|------|
| 1 | Phase 1 | 项目脚手架（Vite + 依赖 + 全局样式 + API层） | — |
| 2 | Phase 2 | 路由 + Pinia Auth/Cart Store + 布局组件 | Phase 1 |
| 3 | Phase 3 | 全局共享组件（Loading/Empty/Error/Badge等） | Phase 2 |
| 4 | Phase 4 | 登录 + 注册 | Phase 2+3 |
| 5 | Phase 5 | 首页 + 书籍详情 | Phase 2+3 |
| 6 | Phase 6 | 购物车 + 确认下单 | Phase 2+3+5 |
| 7 | Phase 7 | 我的订单 + 订单详情 | Phase 2+3 |
| 8 | Phase 8 | 我的卖书 + 提交卖书 | Phase 2+3 |
| 9 | Phase 9 | 求购广场 + 详情 + 发布 | Phase 2+3 |
| 10 | Phase 10 | 个人中心 | Phase 2+3 |
| 11 | Phase 11 | SPA fallback + Blade 逐步替换 | All above |
