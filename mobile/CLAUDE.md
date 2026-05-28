# mobile/ — uni-app 移动端

校园二手书平台 uni-app 移动端，Vue 2 Options API，14 页面，3 tabBar。HBuilder X 原生项目格式。

## 运行（唯一方式，禁止用其他方式）

1. 打开 HBuilder X
2. 打开 `mobile` 项目
3. **运行 → 运行到小程序模拟器 → 微信开发者工具**

**绝对禁止：** `npm run dev:h5`、`npm run build:mp`、Vite、webpack CLI、终端编译命令。不符合此流程的改动一律算错误。

## 代码规范

- **Vue 2 Options API**：`<script>` + `export default { data(), methods, computed, mounted }`
- **禁止** `<script setup>`、`ref`、`computed()`、`onMounted()`（HBuilder X 内置编译器是 Vue 2）
- **状态管理**：`stores/auth.js` 用 `Vue.observable()`，页面 `import auth from '@/stores/auth.js'`
- **CSS**：不用 `calc()` 和 `gap`（微信小程序不兼容），用 `width:49%` + `margin-bottom` + `box-sizing:border-box`

## 项目结构

```
mobile/
├── manifest.json / pages.json / App.vue / main.js / uni.scss   ← 根目录
├── pages/           # 14 页面（全部 Options API）
├── utils/request.js # API 封装（#ifdef MP-WEIXIN 设绝对路径）
├── stores/auth.js   # Vue.observable 单例
└── static/          # 81x81 PNG tabBar 图标（Material Design）
```

## 依赖

仅 `vue: ^2.6.11`，无需其他 npm 包。HBuilder X 内置提供 uni-app 运行时和编译器。

## 设计 Token（轻奢学院风）

| Token | 值 | 用途 |
|-------|-----|------|
| 暖棕 | `#2c2416` / `#3d3020` | 主文字、深色背景 |
| 香槟金 | `#b49450` / `#d4bc7c` | 品牌主色、CTA按钮、价格 |
| 酒红 | `#6b2737` / `#8b3a4a` | 强调色、价格、危险操作 |
| 苔绿 | `#4a6741` / `#5a7d51` | 成功状态、求购模块 |
| 暖珊瑚 | `#e07b5a` | 快捷入口渐变、提醒 |
| 奶油底 | `#fdfaf4` | 页面背景 |
| 暖白卡片 | `#fffdf9` | 卡片、表单背景 |
| 墨色 | `#2c2416` | 主文字 |
| 暖灰 | `#8c8478` | 辅助/弱化文字 |
| 暖边框 | `#e5dccf` | 边框、分割线 |
| 成功绿 | `#4a6741` | 成功/在售 |
| 危险红 | `#6b2737` | 错误/驳回/删除 |
| 警告金 | `#8b6914` | 待处理/待付款 |

### 全局工具类（uni.scss）

- `.card-accent-{blue,amber,teal,coral,indigo,green,red,gray}` — 卡片左侧 3px 彩色装饰条
- `.card-top-{teal,amber,wine}` — 卡片顶部 3px 彩色条
- `.gradient-{blue-indigo,blue-teal,amber-coral,warm}` — 渐变背景头部
- `.btn-teal` — 苔绿渐变按钮（求购专用）
- `.btn-wine` — 酒红渐变按钮
- `.btn-primary` — 金色渐变按钮
- `.badge-{success,warning,danger,info,teal,coral,indigo}` — 语义标签
- `.text-{amber,teal,coral,indigo,danger,success,muted,dark,wine}` — 文字颜色

## Vue 事件传参陷阱

`@click="fn"` 会把 click 事件对象作为第一个参数传入。如果 fn 的参数期望非事件值（如 `fn(isNew)`），事件对象 truthy 会导致逻辑错误。必须显式传参：`@click="fn(false)"`。
