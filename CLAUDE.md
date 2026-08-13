# CLAUDE.md

校园二手书交易平台 — 平台自营模式，Laravel 5.8 + Blade + uni-app + MySQL 全栈项目。

## 项目路径

`D:\MySubject\Schoolcampus_books\` — 后端 `backend/`，移动端 `mobile/`（2026-05-28 从 Desktop 搬至此）

## 开发环境

| 项目 | 值 |
|------|-----|
| MySQL | 小皮（phpstudy），127.0.0.1:3306（凭据见 .env） |
| 数据库 | campus_books（InnoDB + utf8mb4） |
| 管理工具 | DBeaver |
| 管理员 | 凭据见 .env（ADMIN_USERNAME / ADMIN_PASSWORD） |
| PHP | 7.3.4 |
| Node | v24.15.0 / npm 11.12.1 |
| uni-app | Vue 2 Options API + HBuilder X，15 页面 + 3 tabBar，`cd mobile && npm run dev:h5` |

## 启动方式

```bash
# 1. 启动小皮 MySQL + Apache（站点根目录 → backend/public）
#    Web 服务运行在 127.0.0.1:80

# 2. 移动端 H5 开发（API proxy → 127.0.0.1:80）
cd mobile && npm run dev:h5
# 访问 http://localhost:8080

# 3. HBuilder X 运行到微信开发者工具（菜单：运行 → 运行到小程序模拟器）
```

## 铁律

0. **移动端唯一运行方式**：HBuilder X → 运行 → 运行到小程序模拟器 → 微信开发者工具。**禁止用 CLI 命令或 Vite 工具链**，代码必须是 Vue 2 Options API + HBuilder X 原生项目格式（文件在根目录，无 `vite.config.js`，无 `src/`，`package.json` 仅 `vue: ^2.6.11`）。不是这个流程的改动一律视为出错。
1. **设计先行**：设计文档全部完成并经用户确认后，才能开始写代码。禁止跳步直接迁移/controller。
2. Laravel 5.8 需要 `Schema::defaultStringLength(191)`（AppServiceProvider 已配）。
3. utf8mb4 下单个索引最长 191 字符，复合索引注意总长 ≤ 768 字节。
4. **只收课本**：平台只卖教材/课本，不收课外书（小说、漫画等）。
5. **学院分类**：书籍按学院分类（非传统图书分类），导航用下拉框选择学院。
6. **前端开发**：所有前台页面走 Blade 模板（`resources/views/web/`），管理后台走 Blade（`resources/views/admin/`）。
7. **卖书图片**：2-5 张，至少封面+内页/背面。Blade sell 页用 FormData+fetch 提交（非传统 form submit），422 时渲染行内错误。
8. **API 测试**：curl 必须带 `-H "Accept: application/json"`，否则 Laravel 返回 HTML 重定向。Windows 终端传中文 JSON 用 `-d @file` 方式避免编码问题。
9. **改完验证**：批量修改后反向 grep 旧值确认零残留，非简单改动调用 `code-review` skill。
10. **表名陷阱**：`order_timeline` 是单数表名，Model 需显式 `$table = 'order_timeline'`。
11. **uni-app 已改为 Vue 2 Options API**：HBuilder X 内置微信小程序编译器是 Vue 2，代码必须用 `<script>` + `export default { data(), methods, computed, mounted }` 写法。**禁止** `<script setup>`、`ref`、`computed()`、`onMounted()` 等 Vue 3 语法。项目结构是 HBuilder X 原生格式（文件在根目录，无 `vite.config.js`，无 `src/`），`package.json` 仅需 `vue: ^2.6.11`。
12. **uni.request 响应格式**：`{data, statusCode, header}` 单对象，不是 `[err, res]`。`data` 是 API 返回的 JSON body。
13. **uni.scss 显式引入**：`src/App.vue` 中 `<style lang="scss">@import '@/uni.scss';</style>`，否则不生效。
14. **uni-page-body 背景**：需 `!important` 覆盖 uni-app 内置 `background-color: transparent`。
15. **catch 块禁止为空**：至少 `console.log` 错误，否则 bug 被静默吞掉。
16. **前端页面验证 = 浏览器实测**：Playwright 打开页面，检查标题/DOM/数据/样式，curl HTTP 200 不算测试。
17. **Blade 页面 JS 禁止全局选择器**：`document.querySelector('form')` 会命中布局里的搜索/登出表单。必须 `<form id="xxx">` + `#xxx` 选择器。排查时 console 跑 `document.querySelectorAll('form')` 看有几个。
18. **books.price / cost_price 是 nullable**：学生提交时不设售价（管理员审核时定价），migration 已改为 nullable。
19. **Blade 控制器加 `expectsJson()` 判断**：fetch 发 `Accept: application/json` 时返回 JSON 而非 302 重定向。
20. **改动后必须自测通过再交付**：改完用 Playwright/curl 实测，拿到证据才能说"好了"。
21. **PHP 7.3 不支持箭头函数**：`fn($x) => $x` 是 PHP 7.4+ 语法，本项目用传统 `function ($x) { return $x; }` 或 foreach 替代。
22. **Blade `@php @endphp` 禁止与 `{{ }}` 紧挨同行**：`@php ... @endphp{{ ... }}` 同行会导致 Laravel 5.8 Blade 编译器解析失败，输出原始代码。`@php` 块必须独立成行，或把变量定义提前到循环外部。
23. **`@csrf` 禁止出现在 HTML 属性值中**：`onclick="...f.innerHTML='@csrf'..."` 中 `@csrf` 生成的 `"` 会闭合 `onclick` 属性，导致 DOM 结构破坏。正确做法：`f.innerHTML='<input type=hidden name=_token value={{ csrf_token() }}>'` — 手动写无引号 HTML + Blade 输出纯 token 值。
24. **搜索框聚焦色用深蓝**：搜索框 focus 用 `#2c5282`（深蓝），不要用琥珀 `#c7915c`，否则和登录表单聚焦态混淆。
25. **学生登录/注册页是独立页面**：不再 `@extends('web.layouts.app')`，与管理后台登录页一样的深蓝渐变背景+Logo Banner+卡片+琥珀按钮结构。底部保留"管理后台"小字入口。管理员登录页底部保留"学生登录"入口实现双向互通。
26. **审核通过直接上架**：`BookService::approve()` 直接设 `status = 'active'`，不再需要单独"确认入库"步骤。
27. **学院分类自动同步**：`CollegeController` 增/改/删时自动同步 `categories` 表，确保首页下拉框数据一致。
28. **订单状态已简化**：`completed`（已完成）状态已移除，`picked_up`（取书即完成，同时结算卖家）。状态流：pending → paid → confirmed → picked_up → 完结。评价独立，不再改变订单状态。
29. **卖书预填**：`/sell?title=&author=&publisher=&category_id=` 支持从求购详情跳转并自动填好书名/作者/出版社/学院。
30. **求购发布验证**：发布求购时检查同书名是否有 active 在售书籍，有则提示"该书已有用户在售"，引导直接购买。
31. **订单卖家可见**：订单列表和详情对卖家也可见（通过 order_items → book → seller_id），不只是买家。卖家通过"我的卖书→查看订单"进入。
32. **OrderTimeline 的 dates**：`$timestamps = false` 时 `created_at` 不会被自动转 Carbon，需手动加到 `protected $dates = ['created_at']`。
33. **后台返回按钮**：所有 admin 子页面（编辑/详情）必须有琥珀渐变返回按钮 `← 返回xxx管理`，放在面包屑下方。
34. **求购加学院**：wants 表有 `category_id`，发布求购时可选学院，详情和卡片显示学院名，卖书预填也带学院。
35. **所有删除都是软删除**：所有 Model 必须 `use SoftDeletes`，所有表必须有 `deleted_at` 列。`$model->delete()` 自动设时间戳，不真删数据。新增删除功能前先确认 Model + 表已配软删除。
36. **Admin 筛选下拉框**：所有管理后台列表页的 `<select>` 筛选器必须加 `onchange="this.form.submit()"`，选了自动提交不用点搜索按钮。
37. **只准 7 个学院**：电子信息工程学院、机电工程学院、财经与物流管理学院、环境与食品学院、汽车工程学院、贸易与旅游学院、艺术学院。Seeder 里不得出现其他学院名。
38. **WXML 不支持 `[0]` 下标**：取字符串首字符用 `.charAt(0)` 或 computed。
39. **微信小程序 API 需要绝对路径**：`utils/request.js` 中 `#ifdef MP-WEIXIN` 设 `BASE_URL = 'http://127.0.0.1'`。
40. **微信小程序 CSS 兼容**：不用 `calc()` 和 `gap`，改用 `width:49%` + `margin-bottom` + `box-sizing:border-box`。
41. **tabBar 图标 81x81 PNG**：微信小程序不支持字体图标，用 `sharp` 将 SVG 转 PNG。
42. **Vue `@click` 事件传参陷阱**：`@click="fn"` 会把 click 事件对象作为第一个参数传入 fn。如果 fn 期望非事件值（如 `fn(isNew)` 判断 truthy/falsy），事件对象为 truthy 会导致逻辑错误。必须显式传参：`@click="fn(false)"`。uni-app 和 Web Vue 均适用。
43. **微信小程序密码框**：`type="password"` 在微信小程序中不生效，必须用 `:password="true"`。
44. **微信小程序 HTTP 图片**：新版基础库禁止 `<image>` 加载 HTTP 图片。已封装 `safe-image` 全局组件（`uni.downloadFile` → 本地临时文件后显示），所有图片显示必须用 `<safe-image>` 替代 `<image>`。
45. **401 需清本地状态**：`utils/request.js` 中 401 拦截器必须同时调用 `auth.logout()` 清空内存中的用户状态，否则页面显示旧数据但接口报未登录。**但 401 拦截器不得强制 `navigateTo` 登录页**，让各页面自行处理 401 UI（如 profile.vue 的 `v-if="!auth.isLogin()"` 登录卡片）。
46. **tabBar 页面不能用 navigateTo**：必须用 `switchTab` 跳转。如需从 tabBar 页面跳转到独立子页面（如"我的求购"），需创建非 tabBar 的独立页面。
47. **微信小程序多图上传**：`uni.uploadFile` 每次只支持一个文件。多图提交走两步流程：① 逐文件 `uni.uploadFile` → `/api/upload` 获取 URL ② `uni.request` JSON POST `image_urls` 提交书数据。后端 `BookService::submit()` 已支持文件对象和路径字符串双模式。
48. **软删除全量覆盖**：**所有** Model 必须 `use SoftDeletes`，**所有**表必须有 `deleted_at` 列。包括关联表（book_images/order_items/order_timeline/reviews/wants/want_fulfillments），不只是主表。新增 Model/表时第一步就加。
49. **error() 参数顺序**：`error(int $code, string $message)` — HTTP 状态码在前，消息在后。`$this->error('消息', 403)` 会导致 JSON body 中 code="消息"、message=403，语义颠倒。
50. **订单删除需校验状态**：API/Web/Admin 三个端的 delete 方法必须检查 `in_array($order->status, ['cancelled', 'picked_up'])`，进行中的订单不允许删除。
51. **移动端 catch 块必须有 toast**：所有 `catch (e)` 必须 `uni.showToast({ title: e.message, icon: 'none' })`，不能只 `console.log`。用户操作失败无感知=功能缺陷。
52. **safe-image 外层需包裹 view**：微信小程序自定义组件 class 不透传，`<safe-image>` 必须用 `<view class="xxx">` 包裹并设固定宽高（`overflow: hidden`），否则图片高度为 0 文字会覆盖上去。
53. **登录/注册页防滚动**：`pages.json` 中设 `"disableScroll": true`，容器 CSS 用 `height: 100vh; box-sizing: border-box;`（非 `min-height`），否则 100vh 含导航栏高度导致溢出滚动。
54. **URL 参数需手动解码**：uni-app 微信小程序 `onLoad(options)` 不会自动 URL-decode 中文参数，接收方必须 `decodeURIComponent()`。
55. **Storage 图片兜底路由**（2026-06-15）：`routes/web.php` 末尾已加 `Route::get('storage/{path}', ...)` 兜底路由。Junction 正常时 Apache 直接 serve（零开销），Junction 损坏时 Laravel 从 `storage/app/public/` 读文件返回。**换机器/重装/任何情况图片都不会破裂**，无需手动建 Junction。路径穿越已做防护（realpath 校验）。
56. **Windows 符号链接**（可选）：如需最高性能（Apache 静态文件 serve），用 PowerShell `New-Item -ItemType Junction` 创建 `public/storage` → `storage/app/public` 联结。但不建也能正常工作（兜底路由接管）。
57. **`.htaccess` 保护**：Laravel 默认 `.htaccess` 可能被意外清空。启动前检查 `backend/public/.htaccess` 非空，内容缺失从 git 恢复。
58. **`rejected` ≠ `removed`**：审核驳回（`rejected`）和下架（`removed`）是两个独立状态。所有 admin 视图的 badge/label 映射和筛选下拉必须同时包含两者，驳回原因对两个状态都要显示。全局禁止「已下架/驳回」合并写法。
59. **Seeder 订单号格式**：必须与 `OrderService::generateOrderNo()` 一致 — `date('YmdHis') . sprintf('%04d', random_int(0, 9999))`。禁止 `ORD` 前缀格式。
60. **平台收购订单无评价**：`buyer->role === 'admin'` 的订单不显示评价入口、不允许提交评价。ReviewService/API/Web/Mobile 四端均需判断。
61. **Layout 学院下拉框上下文**：`app.blade.php` 的学院 `<select>` 和搜索框仅在非 `/wants*` 页面显示，避免在求购页误触跳回首页。
62. **Admin 操作按钮规范**：所有状态统一显示「编辑」按钮（非「查看」），仅 `active` 附加「下架」、`rejected` 附加「删除」。
63. **微信小程序页面隔离 — 禁止用 Vue.observable 跨页共享状态**：微信小程序每页独立 JS 上下文，模块级 `Vue.observable` 在不同页面是不同对象。`stores/auth.js` 的 `isLogin()`、`get token()`、`get user()` 必须从 `uni.getStorageSync('auth')` 读取，`state` 仅作当前页内响应式辅助。**H5 开发模式无此问题**（浏览器单上下文），所以这个 bug 在 HBuilder X 模拟器里不可见，真机/预览时才暴露。

### uni-app 移动端（15 页 + 3 tabBar）
- 15 个页面：首页/登录/注册/书籍详情/卖书/购买/订单列表/订单详情/评价/我的卖书/求购广场/求购详情/发布求购/我的求购/个人中心
- tabBar 导航（首页/求购/我的），Material Design 81x81 PNG 图标
- API 封装（`utils/request.js`，#ifdef MP-WEIXIN 绝对路径，401 清 auth 但不强制跳转）+ auth store（`stores/auth.js`，用 `uni.getStorageSync` 跨页共享，非 `Vue.observable`）
- **运行唯一方式**：HBuilder X → 运行 → 运行到小程序模拟器 → 微信开发者工具。禁止 CLI/Vite
- **设计系统**（2026-05-28 轻奢学院风）：香槟金+酒红+暖棕品牌色 + 苔绿(#4a6741 求购)/暖珊瑚(#e07b5a 快捷入口)/靛紫(#5b7fbd 分类) 辅助色
- 全局工具类：`.card-accent-*`（8 色卡片左边框）、`.card-top-*`（顶部彩色条）、`.gradient-*`（渐变头部）、`.btn-teal`（青绿按钮）
- CSS 兼容：全站 `gap`/`calc` 已替换为 `margin`/`padding`，密码框 `:password="true"`
- **全局组件**：`safe-image`（uni.downloadFile 下载 HTTP 图到本地）
- **个人中心**（2026-05-28 风格统一）：暖棕渐变头部+头像+学号+脱敏手机号、4 格订单统计卡片（可点击按状态筛选）、菜单分组（交易管理+更多）。**快捷入口 4 圆已移除**（2026-05-29）
- **我的求购**（2026-05-29）：独立页面 `pages/wants/mine`（非 tabBar），调用 `/api/my-wants` 仅显示自己的求购
- **订单详情**：支持买家/卖家双视角（is_buyer/is_seller），已移除书籍封面图（纯文字布局）
- **搜索框清除按钮**（2026-05-29）：首页+求购广场，`<view>` + `@click.stop` + `onClear` 方法（24×24 圆形 ×，z-index:2），避免微信 `<text>` 点击被 input 截获
- **卖书多图上传**（2026-05-29）：H5 端 FormData 一次性提交不变；微信小程序端两步上传（逐文件 `uni.uploadFile` → `/api/upload` → 收集 URL → JSON POST `image_urls`），后端 `BookService::submit()` 同时支持 `UploadedFile` 对象和路径字符串
- **首页加载更多**（2026-05-27 修复）：page 自增 + `@click="fetchBooks(false)"` 防止事件对象误传
- **订单列表**：支持 `?status=` 参数筛选（统计卡片点击跳转），订单统计双视角计数

### Blade 后台（已完成）
- 管理后台 7 模块（dashboard/books/orders/users/wants/categories/colleges），暖棕侧边栏+香槟金设计系统
- 管理员登录页底部链接：学生登录 / 返回首页
- **用户管理**（2026-05-24）：编辑按钮+独立编辑页（姓名/学号/手机/角色/状态/密码）；**删除按钮**（软删除，不能删自己）；角色/状态下拉自动提交
- **分类管理**（2026-05-24）：编辑改为跳转独立页面，不再用 prompt 弹窗
- **求购管理**（2026-05-24）：删除按钮（含确认弹窗，级联删除接单记录）；状态下拉自动提交
- **订单管理**：详情页左右等高（`.flex-row` 改为 `stretch`）；状态下拉自动提交
- **书籍管理**：状态下拉自动提交
- **审核管理**：状态下拉自动提交
- **返回按钮规范**：所有后台子页面（编辑/详情）统一琥珀渐变返回按钮

### Blade 前台（纯 Blade）
- 12 个 Blade 视图 + 1 个布局，路由在 `web.php`
- **Vue SPA 已移除**（2026-05-24）：删除 `frontend/` 目录，纯 Blade 架构
- **首页**：Hero Banner + 滚动提示 + 排序下拉（最新/价格升降/成色）+ "立即购买"按钮（原"加入购物车"已改为直接购买）
- **卖书提交流程已修复**（2026-05-22）：CSRF、文件上传、表单清空、成功提示均正常
- **学生登录/注册页**（2026-05-23）：独立页面，暖棕渐变背景+Logo Banner+香槟金按钮，底部含"管理后台"入口
- **求购模块**：求购广场列表 + 详情页 + "我要卖这本书"按钮（带学院预填）+ 发布求购页（学院选择+在售验证）+ 导航栏求购入口
- **订单模块**（2026-05-29）：买家+卖家双视角，取消按钮仅买家可见，删除按钮买卖双方可见（cancelled/picked_up），我的卖书→查看订单入口，动态删除提示文案
- **书籍删除**（2026-05-29）：removed/rejected 状态均可删，动态确认文案区分"被下架"/"被驳回"

### 数据库
- 已建 `campus_books`，18 张表全部 migrate（含 `add_soft_deletes`、`add_soft_deletes_to_users`、`add_soft_deletes_to_orders`、`add_category_id_to_wants`）
- 测试数据（2026-05-29 更新）：3 学生 + 1 管理员，52 书籍（44在售+8已售），10 订单，12 求购，5 评价
- **Seeder 已修正**：7 个真实学院（电子信息、机电、财经物流、环境食品、汽车、贸易旅游、艺术），每个学院有对应专业和课程，书籍 `course_id` 匹配正确学院
- **软删除已覆盖**：books、categories、colleges、majors、courses、users、orders

### 账户
- 测试管理员与测试学生账号凭据见 `.env`（admin 用户名或手机号 13800000000，学生 13800000001 ~ 13800000003）

## 设计系统（2026-05-28 轻奢学院风）

| Token | 值 | 用途 |
|-------|-----|------|
| 香槟金 | `#b49450` / `#d4bc7c` | 品牌主色、CTA按钮、价格 |
| 暖棕 | `#2c2416` / `#3d3020` | 主文字、深色背景、导航栏 |
| 酒红 | `#6b2737` / `#8b3a4a` | 强调色、价格、危险操作 |
| 苔绿 | `#4a6741` / `#5a7d51` | 求购模块、成功状态 |
| 暖珊瑚 | `#e07b5a` | 快捷入口渐变、提醒 |
| 奶油底 | `#fdfaf4` | 页面背景（含径向渐变光晕） |
| 暖白卡片 | `#fffdf9` | 卡片、表单背景 |
| 暖灰 | `#8c8478` | 辅助/弱化文字 |
| 暖边框 | `#e5dccf` | 边框、分割线 |
| 成功绿 | `#4a6741` | 成功/在售/全新 |
| 危险红 | `#6b2737` | 错误/驳回/删除 |
| 警告金 | `#8b6914` | 待处理/待付款 |

字体：Cormorant Garamond（标题）+ 霞鹜文楷 LXGW WenKai TC（正文）+ JetBrains Mono（等宽标签）。圆角 12-24px 胶囊形，✦ 菱形装饰。

CSS 全部内联在 Blade `<style>` 块中。移动端全局样式在 `mobile/uni.scss`。

## 关键路由

| 方法 | 路径 | 说明 |
|------|------|------|
| GET | `/` | 前台首页（Banner+排序+搜索+学院下拉+书籍网格+立即购买） |
| GET | `/search?keyword=&category_id=&sort=` | 搜索（支持排序） |
| GET | `/books/{id}` | 书籍详情（封面/实拍/信息/购买按钮） |
| GET/POST | `/login` | 学生登录（独立页面，深蓝渐变+琥珀按钮） |
| GET/POST | `/register` | 学生注册（独立页面，同登录风格） |
| POST | `/logout` | 退出登录 |
| GET/POST | `/sell` | 卖书（需登录） |
| GET/POST | `/buy/{bookId}` | 购买确认/下单（需登录） |
| GET | `/orders` | 我的订单列表（需登录） |
| GET | `/orders/{id}` | 订单详情+支付/取消（需登录） |
| GET | `/my-sells` | 我的卖书列表（需登录） |
| GET | `/wants` | 求购广场 |
| GET | `/wants/{id}` | 求购详情+"我要卖这本书"按钮（带学院预填） |
| GET/POST | `/post-want` | 发布求购（需登录，有在售同书名验证） |
| POST | `/orders/{id}/delete` | 删除订单（买家+卖家，cancelled/picked_up） |
| GET/POST | `/admin/login` | 后台登录（用户名或手机号） |
| GET | `/admin/{module}` | 后台各模块 |

### API 路由（`backend/routes/api.php`）
| 方法 | 路径 | 说明 |
|------|------|------|
| POST | `/api/auth/register` | API 注册 |
| POST | `/api/auth/login` | API 登录 |
| GET | `/api/auth/me` | 用户信息+统计（stats：订单计数/在售书/求购数） |
| GET | `/api/books?keyword=&category_id=&sort=&page=&per_page=` | 书籍列表（分页+筛选+排序） |
| GET | `/api/books/{id}` | 书籍详情（图片/卖家/评价） |
| POST | `/api/books/submit` | 提交卖书（multipart/form-data） |
| GET | `/api/my-books` | 我的卖书 |
| GET | `/api/orders?status=` | 订单列表（买家+卖家，支持状态筛选） |
| POST | `/api/orders` | 下单（book_ids[]/pickup_location） |
| POST | `/api/orders/{id}/pay` | 支付 |
| POST | `/api/orders/{id}/cancel` | 取消 |
| POST | `/api/orders/{id}/pickup` | 确认取书（结算卖家） |
| POST | `/api/orders/{id}/review` | 评价（book_rating/service_rating/comment） |
| GET | `/api/wants` | 求购列表 |
| GET | `/api/wants/{id}` | 求购详情 |
| POST | `/api/wants` | 发布求购（在售验证返回422） |
| GET | `/api/my-wants` | 我的求购（需认证） |
| DELETE | `/api/orders/{id}` | 删除订单（买家+卖家） |
| GET | `/api/categories` | 学院分类列表 |
| GET | `/api/colleges` | 学院列表 |

## 文档索引

- [需求文档](校园二手书平台-需求文档.docx)
- [需求摘要](docs/REQUIREMENT.md)
- [技术栈](docs/TECH-STACK.md)
- [设计规格](docs/DESIGN-SPEC.md)
- [编码规范](docs/CODING-STANDARDS.md)
- [API 规格](docs/API-SPEC.md)
- [后端架构](docs/BACKEND-DESIGN.md)
- [测试流程](测试流程.txt)
- [使用手册](校园二手书平台-使用手册.docx)
- [开发日志](DEVLOG.md)

## 使用手册格式规范

生成/更新 `校园二手书平台-使用手册.docx` 时，严格遵循以下格式（参照 `yyy/使用说明书.docx`）：

| 元素 | 字体 | 字号 |
|------|------|------|
| 主标题（"校园二手书交易平台"） | 黑体 | 26pt（一号） |
| 副标题（"使用手册"） | 黑体 | 22pt（二号） |
| 版本行 | 仿宋 | 12pt（小四） |
| 章节标题（一、二、三、四） | 黑体 | 继承 |
| 步骤标题（1. 2. 3.） | 黑体 | 14pt（四号） |
| 正文 | 仿宋 | 16pt（三号） |
| 图注（"图 N  描述"） | 宋体 | 继承，居中 |
| 截图占位 | 1×1 表格（Table Grid），行高 7~8cm，单元格内灰色「（插入截图）」 | |

- **东亚字体**：每个 run 必须设 `w:eastAsia` 属性（黑体/仿宋/宋体）
- **内容风格**：像测试流程一样简洁，只说操作步骤，不堆砌技术细节
- **截图位**：每个关键页面/操作步骤后面紧跟一个 1×1 表格作为截图占位，再加图注
- **结构**：一、学生 Web 端 → 二、移动端 → 三、管理后台 → 四、关键验证点

## 全局规则

本项目的全局编码/安全/测试规则位于 `~/.claude/rules/ecc/`，包含 common（通用）、php（PHP 特定）、web（前端特定）三层，开发时自动应用。

## 项目 Skills

- **移动端开发**：修改 uni-app 页面/配置/pages.json/manifest.json 时，必须调用 `uni-app` skill（uni-app 框架参考、组件 API、条件编译、平台适配）
- **Blade 模板 JS**：修改 Blade 模板内联 JS 时，必须调用 `blade-js-pitfalls` skill（DOM 选择器作用域、fetch/FormData、CSRF、错误处理等避坑指南）
- **Skill 安装前**：安装任何新 skill 前，必须先调用 `skill-vetter` 审核（Source Check → Code Review → Permission Scope → Risk Classification），通过后才能装

## 冲突解决声明

### 1. 不可变性覆盖
- ECC 通用规则的"不可变性"在本项目**不适用于 Eloquent ORM**
- `$model->fill($data)->save()` 是 Laravel 标准惯用法，允许原地修改
- 不可变性仅在 DTO、Value Object、Collection 链式操作中推荐使用

### 2. DRY 提取边界
- **允许**：任务直接涉及的文件内部，提取重复代码
- **禁止**：跨模块、跨功能、非任务相关的大范围重构
- 判断标准：改动是否在任务描述的文件清单内？是→可做，否→不做

### 3. PHP 项目跳过 JS/TS Hook
- `stop:format-typecheck` Hook 在此项目中应跳过执行
- 执行条件：仅当存在 `package.json` 且存在 `tsconfig.json` 时才运行

### 4. 配置文件修改规则
- 如需修改 PHPCS、ESLint、Prettier 等配置文件，直接告诉 Claude："临时禁用 config-protection，我要修改配置文件"

### 5. 模型配置（DeepSeek V4-Pro）
- 本项目使用 DeepSeek V4-Pro（1M token 上下文），非 Anthropic 模型系列
- ECC `performance.md` 中的 Haiku/Sonnet/Opus 建议**不适用**
- 替代策略：
  - 日常开发：`deepseek-v4-pro`（性价比最优）
  - 简单任务：`deepseek-v4-flash`（更快、更便宜）
  - 复杂推理：`deepseek-v4-pro` 已足够，无需切换

### 6. GateGuard 豁免规则
- **需要完整调查**：`app/Models/`、`app/Services/`、`app/Repositories/`（非 Laravel 项目适配核心目录）
- **跳过调查，直接编辑**：`resources/views/`、`public/`、`database/migrations/`、`tests/`、`docs/`
- 以下情况跳过调查，直接编辑：
  - 修改字符串、文案、标签
  - 修复拼写错误、调整缩进、添加注释
  - 修改单行代码（如改一个变量名、修复一个语法错误）
  - 创建新文件（非覆盖已有文件）
  - 用户明确说"直接改"或"快速修复"
- 判断规则：业务逻辑变更小于 3 行代码，直接改；否则按原流程调查

### 7. 文档格式明确
- 用户要求的"文本文档""说明文档" → 使用 `.txt`
- 项目结构文档（CLAUDE.md、README.md） → 使用 `.md`
- `doc-file-warning` Hook 对 `.txt` 文件的警告请忽略

### 8. 格式化策略
- 使用 Stop 时批量格式化（`stop:format-typecheck`），不是每次 Edit 后立即格式化
- 优点：减少 Hook 启动开销，避免重复格式化
- 执行时机：每次会话结束时，对所有编辑过的文件批量运行格式化

### 9. 命名规范汇总（优先级：项目 > ECC）

| 层面 | 规范 | 示例 |
|------|------|------|
| PHP 类 | PascalCase | `UserController` |
| PHP 方法/变量 | camelCase | `getUserById()` |
| JS 变量/函数 | camelCase | `fetchBooks` |
| JS 常量 | UPPER_SNAKE_CASE | `API_BASE_URL` |
| CSS 类 | kebab-case | `.book-card` |
| 文件名（PHP/JS） | PascalCase（类文件）/ camelCase（工具文件） | `User.php` / `apiClient.js` |
| CLI 命令 | kebab-case | `codegraph init -i` |
| 数据库表/字段 | snake_case | `user_books` |

ECC `coding-style.md` 中的通用规则作为基准，本表覆盖了项目特定差异。

### 10. CodeGraph 与 grep 分工

| 场景 | 使用工具 | 是否需要验证 |
|------|----------|--------------|
| 查函数定义、调用关系、影响范围 | CodeGraph | 不需要 grep 验证（AST 已保证准确） |
| 查字符串内容、注释、日志 | grep | 需要 |
| 修改后的验证（改完先验证再报告） | grep + 语法检查 | 需要 |

### 11. config-protection 范围确认

`config-protection` Hook 仅拦截 linter/formatter 配置文件（`.eslintrc*`、`.prettierrc*`、`.stylelintrc*` 等）。

它不会拦截：
- `CLAUDE.md`
- `.claude/` 目录下的任何文件
- 项目初始化流程中的任何操作

如有误拦，请告知用户并跳过保护。

## PPT 生成/修改（ppt-master）

64. **减法定制优先改 SVG**：从现成 PPT 删/改某类内容时，直接编辑 svg_final/ 或 svg_output/ 中 SVG 文件，重跑 finalize_svg.py + svg_to_pptx.py 导出。不要重走完整 ppt-master 八步流水线。
65. **clipPath 必须放 `<defs>` 内**：svg_to_pptx.py 对 defs 外的 `<clipPath>` 报 unsupported visual SVG element(s) 错误。
66. **微信开发者工具截图先问模拟器位置**：新版模拟器在**右边**（旧版左边）。
67. **导出必须带 `-t fade -a auto`**：不加动画参数 PPT 无翻页过渡和元素进场效果。
68. **图片容器尺寸匹配源图宽高比**：排版前查源图比例。移动截图 1250x1000（1.25:1）不能放 1.8:1 容器。
69. **Windows CMD 终端适配**：不能用 cp（用 copy）、`&&` 链式不一定生效（逐条给）。

## uni-app / 微信小程序页面隔离（强制）

> 从全局 CLAUDE.md 迁入（本节为本项目 uni-app 端专属，全局只留一行指针）。

**微信小程序每页独立 JS 上下文。** 模块级变量在不同页面是不同对象，不跨页共享。

### Vue.observable 不跨页

`Vue.observable` 在 `stores/auth.js` 顶层创建的 `state` 对象，在 login.vue 和 profile.vue 中是两份独立数据。login.vue 写入 `state.token`，profile.vue 读到的是空字符串。

**规则：**
- `isLogin()`、`get token()`、`get user()` 必须从 `uni.getStorageSync('auth')` 读取
- `state` 仅作当前页内 Vue 响应式辅助，不得用作跨页数据源
- `auth.save()` 同时写 `state.token` + `uni.setStorageSync`
- `auth.logout()` 同时清 `state` + `uni.removeStorageSync`

**陷阱：H5 开发模式无此问题。** 浏览器单上下文，`Vue.observable` 跨页完美工作。bug 只在微信小程序真机/预览暴露。

### 401 拦截器只清状态，不强制跳转

`utils/request.js` 401 拦截器必须调用 `auth.logout()` 清状态，但**不得强制 `navigateTo` 登录页**。让各页面自行处理 401 UI（如 `v-if="!auth.isLogin()"` 显示登录卡片），避免双层跳转冲突。

### uni-app 项目结构

- 页面 `pages/`，组件 `components/`，API 封装 `utils/request.js`
- API URL 通过 `BASE_URL` 统一管理，页面禁止硬编码
- 环境切换时全局搜索远程 IP → 逐一改源文件 → grep 确认
