# 开发日志

## 2026-05-29

### 今日完成
- **Git 初始化 + Worktree 隔离**：项目 git init，创建 `.worktrees/` 隔离开发环境
- **订单软删除**：orders 表加 `deleted_at`，Order Model 加 SoftDeletes，Blade + API 双端支持删除（买家+卖家，cancelled/picked_up 状态）
- **OrderTimeline created_at 修复**：`$fillable` 补 `created_at`，所有 `create()` 调用显式传 `now()`
- **BookService::reject() 修复**：`status = 'removed'` → `'rejected'`（关键 bug）
- **导航栏颜色区分**：navbar 与首页背景色做区分（羊皮纸色 `rgba(247,241,230,0.94)`）
- **Admin 分页 CSS 修复**：`.pagination .page-link` 选择器，搜索框聚焦色统一深蓝
- **Admin 订单管理**：新增删除按钮（cancelled/picked_up）+ JS `deleteOrder()` 函数
- **Admin 求购管理**：新增编辑按钮 + 独立编辑页（title/author/publisher/category/price/condition/status）
- **移动端 - safe-image 全局组件**：`uni.downloadFile` 下载 HTTP 图片到本地临时文件后显示，解决微信新版基础库 HTTP 阻断
- **移动端 - 我的求购**：独立页面 `pages/wants/mine.vue`（非 tabBar），调用 `/api/my-wants`
- **移动端 - 个人中心优化**：移除 4 个快捷入口圆形按钮，"求购广场"→"我的求购"
- **移动端 - 搜索清除按钮**：首页+求购广场，× 按钮（改为 `<view>` + `@click.stop` + `onClear` 方法，24×24，z-index:2）
- **移动端 - 搜索框布局修复**：输入框 `box-sizing:border-box`，按钮 `flex-shrink:0`，不会再挡住搜索按钮
- **移动端 - 密码可见性修复**：`type="password"` → `:password="true"`（微信小程序兼容）
- **移动端 - 订单详情**：买家/卖家双视角（is_buyer/is_seller），移除书籍封面图（纯文字布局）
- **移动端 - 401 处理**：`request.js` 拦截器加 `auth.logout()` 清本地状态
- **移动端 - 全量代码审查**：审查 15 个页面 + request.js + auth.js，发现并修复 5 个 bug（见下方）
- **移动端 - 多图上传改造**：微信小程序端从单文件改为两步上传（逐文件→`/api/upload`→收集 URL→JSON POST `image_urls`），后端 `BookService::submit()` 支持 `UploadedFile` 和路径字符串双模式
- **Bug 修复 - sell.vue**：`image_types` 硬编码 `['cover','other']` 改为 `for` 循环动态生成
- **Bug 修复 - request.js**：空 catch 块 `/* ignore */` 改为 `console.log`
- **Bug 修复 - safe-image.vue**：`.ph` 占位符补 `width:100%;height:100%`
- **Bug 修复 - detail.vue**：`.bar-info` CSS 重复定义合并
- **订单统计双视角**：`orderCount()` 同时查 buyer_id + seller_id（通过 order_items.book）
- **Web 端动态删除提示**：removed 提示"删除被下架的书"，rejected 提示"删除被驳回的书"
- **卖家订单入口**：我的卖书→查看订单（`/orders/{id}?from=sells`），详情页返回按钮动态跳转
- **测试数据精简**：16 学生 → 3 学生（13800000001~3），密码统一 REDACTED-PASSWORD
- **使用手册更新**：v2.0，42 截图位，新增移动端专属验证点/卖家订单/多图上传/软删除章节

### 待办事项
- [ ] HBuilder X 运行移动端验证：搜索清除按钮、多图上传、全部页面

### 遇到的问题
- **OrderTimeline created_at NULL**：`$timestamps = false` + `$fillable` 缺 `created_at` → 批量赋值静默丢弃。修复：补 $fillable + 显式传 now()
- **BookService::reject() 状态错误**：设了 `removed` 应设 `rejected`，导致驳回书无法区分
- **订单取消 404（卖家）**：取消按钮卖家可见但 API 仅查 buyer_id。修复：Blade `@if($order->buyer_id === auth()->id())` + API 显式 403
- **微信小程序 HTTP 图片**：新版基础库阻止 HTTP。解决：safe-image 组件 + uni.downloadFile
- **密码明文可见**：`type="password"` 微信不生效。解决：`:password="true"`
- **订单统计不准**：stats 仅计 buyer 但列表双视角显示。解决：orderCount() 双视角查询
- **API /wants 无 auth**：公开路由 `auth()->check()` 始终 false。解决：新增 `/api/my-wants` 在 auth 中间件内
- **搜索清除按钮点击无效**：`<text>` 元素在微信中点击被 input 拦截。解决：`<view>` + `@click.stop` + 独立 `onClear` 方法
- **搜索输入框挡住按钮**：`flex:1` + `margin-right` 在微信中布局异常。解决：`flex-shrink:0` + `margin-left:8px` + `box-sizing:border-box`
- **微信小程序多图上传**：`uni.uploadFile` 单次只支持一个文件，`uploadSequential` 只传了 `files[0]`。解决：两步上传（逐文件 → /api/upload → JSON POST image_urls）+ 后端 BookService 支持路径字符串

## 2026-05-28

### 今日完成
- **全站风格迁移**：深蓝+琥珀 → 轻奢学院风（香槟金+酒红+暖棕+奶油色），用户反馈旧风格太单调
  - Blade 前台：14 文件（布局 + 13 页面），暖棕毛玻璃导航、香槟金胶囊按钮、酒红价格、✦菱形装饰
  - Blade 后台：15 文件（布局 + 14 页面），暖棕侧边栏、香槟金表格/徽章/统计卡片
  - uni-app 移动端：16 文件（uni.scss + pages.json + 14 页面），CSS token 全量替换
  - 字体：Google Fonts 引入 Cormorant Garamond（标题）+ 霞鹜文楷 LXGW WenKai TC（正文）+ JetBrains Mono（等宽标签）
  - 背景纹理：SVG 噪声 → radial-gradient 三层光晕
  - 圆角：8-12px → 12-24px 胶囊形
- 项目 CLAUDE.md 设计系统表更新为新 token
- memory/design-system.md + login-page-architecture.md 配色更新

### 待办事项
- [ ] HBuilder X 运行移动端验证新配色

### 遇到的问题
- 批量替换时 login/register 背景渐变 `#1e3a5f` → `#b49450` 效果不对（深蓝渐变变成金色），手动改为暖棕渐变 `#1a1510` → `#2c2416` → `#3d3020`

### 今日完成
- 个人中心页面优化：新增订单统计卡片（待付款/已付款/待取书/已完成，可点击筛选）、4 格快捷入口（卖书/求购/在售/求购数）、菜单分组（交易管理+更多），深蓝渐变头部+头像+学号+脱敏手机号
- `/api/auth/me` 接口增强：返回订单状态计数（order_pending/paid/confirmed/done）、在售书籍数、求购数
- 首页"加载更多"修复：`this.page++` 自增 + `@click="fetchBooks(false)"` 防止事件对象误传
- 订单列表支持 `?status=` URL 参数筛选（`onLoad` 解析 → API 请求）
- 全局移动端配色优化：
  - 新增 3 个辅助色系：青绿 `#0d9488`（求购/成功）、靛紫 `#5b7fbd`（分类/信息）、暖珊瑚 `#e07b5a`（快捷入口）
  - `uni.scss` 新增 `card-accent-*`（8 色卡片左边框）、`card-top-*`（顶部彩色条）、`gradient-*`（渐变头部）、`btn-teal`（青绿按钮）、`badge-teal/coral/indigo`
  - 14 个页面全部优化：彩色卡片装饰条、渐变色按钮、状态颜色区分、区域标题琥珀左边框
  - 全站 `gap`/`calc` 替换为 `margin`/`padding`（微信小程序兼容）
- 使用手册：按 yyy/使用说明书.docx 格式重制 — 黑体标题+仿宋正文+宋体图注+1×1表格截图位（36个），东亚字体正确设置
- 使用手册格式规范写入 CLAUDE.md（字体/字号/结构/风格），后续更新严格遵循
- 需求文档字体统一改为小二（18pt）
- 项目 CLAUDE.md 更新：当前状态→2026-05-27、设计系统扩展 3 色、API 路由表扩展到 18 条、新增铁律 #42 Vue 事件陷阱、使用手册格式规范

### 待办事项
- [ ] HBuilder X 运行到微信开发者工具，逐页验证配色效果
- [ ] 检查首页加载更多的翻页是否正常（第1页→第2页→第3页→尾页隐藏按钮）

### 遇到的问题
- 首页"加载更多"点击无效：根因是 `@click="fetchBooks"` 把事件对象作为 `isNew` 传入（truthy），导致每次都重置 page=1。改为 `@click="fetchBooks(false)"` 解决。

## 2026-05-26

### 今日完成
- HBuilder X 编译调试成功：确认 `uniapp-cli` 内置编译器使用 Vue 2，CLI 项目 `uniapp-cli-vite` 使用 Vue 3
- 14 个页面全部从 Vue 3 Composition API 改为 Vue 2 Options API（`<script>` + `export default`）
- HBuilder X "运行到微信开发者工具" 成功运行
- 首页学院筛选改为下拉列表（web 风格，带遮罩层）
- 登录/注册页表单垂直居中
- 书籍网格修复为两列布局（`width:49%` + `flex-wrap:wrap`）
- tabBar 图标用 Material Design SVG → sharp 转 81x81 PNG（首页/搜索/用户）

- 安装 `uni-app` skill（uni-helper/skills，1.2K 安装量），注册到项目 CLAUDE.md
- 建立 skill-vetter 审核流程：安装任何 skill 前必须先审查安全（全局 CLAUDE.md + 项目 CLAUDE.md + 记忆）
- 首页筛选改为 web 风格下拉列表，登录/注册页居中，tabBar 图标用 Material Design
- 配置清单整理：2 个 CLAUDE.md + 34 条记忆 + 32 个 ECC 规则 + 50+ skills

### 遇到的问题及解决方案
1. **HBuilder X "node_modules缺少编译器模块"** → 项目结构不对（文件在 `src/`），改为 HBuilder X 原生格式（文件在根目录）
2. **微信小程序 `_vue.ref is not a function`** → HBuilder X 内置 mp-wexin 编译器是 Vue 2，代码需用 Options API
3. **WXML 不支持 `[0]`** → 改用 `.charAt(0)` + computed
4. **微信小程序 tabBar 不能用字体图标** → SVG 转 PNG，81x81
5. **书籍网格单列显示** → 微信不支持 `calc()`/`gap`，改用 `width:49%` + `margin-bottom`
6. **Pinia webpack 编译失败** → 弃用，改用 `Vue.observable()`
7. **GitHub API 被墙无法装 skill** → 开加速器后 `npx skills add` 成功

## 2026-05-25

### 今日完成
- uni-app 移动端 14 页面全部开发完成：首页/登录/注册/书籍详情/卖书/购买/订单列表/订单详情/评价/我的卖书/求购广场/求购详情/发布求购/个人中心
- tabBar 导航（首页/求购/我的），设计风格与 Web 端统一
- CLI 编译通过（Vue 3 + Vite）：H5 (`dev:h5`) + 微信小程序 (`build:mp`)
- HBuilder X 运行已配置：根目录结构 + manifest.json proxy + UNI_INPUT_DIR env

### 遇到的问题及解决方案
1. **@dcloudio 稳定版 `50007` 微信小程序运行时用 Vue 2** → 改用 alpha `50101` 系列（Vue 3），编译器显示"vue3"
2. **Pinia `pinia.mjs` 在 webpack 编译时找不到** → 弃用 Pinia，改用 Vue 原生 `ref` 模块（`stores/auth.js` export 单例 refs）
3. **WXML 不支持 `[0]` 下标** → 改用 `.charAt(0)` + computed
4. **`isInSSRComponentSetup` / `injectHook` 未导出** → `postinstall` 脚本 patch `@dcloudio/uni-app/dist/uni-app.es.js`
5. **微信小程序 API 需绝对路径** → `request.js` 条件编译 `MP-WEIXIN` 设 `BASE_URL = 'http://127.0.0.1'`
6. **HBuilder X 需根目录结构** → 文件从 `src/` 移到项目根，`UNI_INPUT_DIR=.` 编译
7. **依赖版本管理**：所有包精确版本 + npm overrides + `postinstall` 自动补丁

## 2026-05-24

### 今日完成
- 修复学院下拉框在订单/卖书页面无选项：AppServiceProvider 注册 View Composer 向所有 uses-layout 页面注入 `$categories`
- 修复 `orders/index.blade.php` 多余 `</style>` 标签
- 修复审核通过的书首页搜不到：`BookService::approve()` 直接设 `status = 'active'`（跳过 `approved` 中间态）
- 审核详情页去掉定价标签里的字段名（price/cost_price）
- 后台书籍管理列表：售价/收书价为 null 时显示 `-`，驳回书显示驳回原因
- 后台书籍编辑页：右边新增"书籍状态"卡片（含驳回原因），左栏 flex-start 对齐
- 学院同步分类：CollegeController 增改删自动同步 categories 表，补齐缺失数据
- 审核管理：新增审核通过书 `received_at` 赋值
- 简化订单状态：去掉 `completed`，`picked_up` 取书即完成（含结算卖家+标记sold），评价独立
- 管理员订单详情：confirmed 状态新增"确认取书（结算卖家）"按钮
- 全局 Blade/Controller/Seeder 清理 `completed` 引用，数据库旧数据转换
- 首页新增求购模块（后移除，改为导航栏入口），新建 `/wants` 列表 + `/wants/{id}` 详情页
- 求购详情页"我要卖这本书"按钮 → `/sell?title=&author=&publisher=` 自动预填
- 首页 Hero Banner 下方新增滚动提示字幕
- **移除 Vue SPA**：删除 `frontend/` 目录，纯 Blade 架构；首页"加入购物车"改为"立即购买"
- **用户管理新增删除**：软删除（users 表加 `deleted_at`，Model 加 SoftDeletes），不能删自己
- **Admin 筛选下拉框**：用户/订单/书籍/审核/求购 5 个模块的 `<select>` 加 `onchange="this.form.submit()"`
- **Seeder 修正**：删除假学院"计算机科学与技术学院"，改为 7 个真实学院+对应专业+课程，书籍 course_id 匹配正确学院
- **订单详情左右等高**：全局 `.flex-row` 从 `align-items: flex-end` 改为 `stretch`
- **测试流程简化**：按学生使用路径重写，去掉 API curl 和 Vue SPA 章节
- CLAUDE.md 新增规则 35-37（软删除/筛选下拉/7学院）

### 待办事项
- [ ] uni-app 移动端开发

## 2026-05-22

### 今日完成
- 修复卖书提交不生效的核心 bug：`document.querySelector('form')` 选中布局搜索框而非卖书表单（页面有3个 form）
- 修复 axios FormData 发送失败：`request.js` 默认 `Content-Type: application/json` 覆盖 multipart boundary
- 修复 Vite proxy target 端口错误：`:8000` → `:80`（项目用 Apache 非 artisan serve）
- 修复 Vue reactive 表单清空失败：`Object.assign` 不触发响应式，改为逐个 key 赋值
- 修复 Laravel `image` 验证规则在 Windows 下误判：改为 `file|mimetypes`
- 新建 migration：`books.price` / `cost_price` 改为 nullable（学生提交时不设售价）
- 卖书提交成功后清空表单 + 绿色成功提示
- 扩充测试数据：用户 9→16，书籍 17→52，订单 4→10，求购 0→12，评价 0→5
- 生成 Word 使用手册（`校园二手书平台-使用手册.docx`）
- 创建 `blade-js-pitfalls` skill，注册到项目 CLAUDE.md
- 更新 CLAUDE.md 铁律 17-24 条
- 更新 CODING-STANDARDS.md（禁止全局 DOM 选择器）

### 待办事项
- [ ] uni-app 移动端开发

## 2026-05-19

### 今日完成
- 修复 route namespace 错误（api.php 应为 `Api` 非 `Auth`）
- 创建前台首页 Web\HomeController + 公共布局 + 首页视图
- 前台页面：导航栏（logo + 学院下拉 + 搜索框 + 卖书/登录按钮）
- 书籍网格展示，4 列响应式布局，分页样式修复
- 搜索框支持关键字搜索 + 清空按钮（修复定位问题）
- 数据库 seed 重写：分类改为 7 个学院，17 本专业课本书籍
- 生成占位封面图（GD 库，400×560 纯色+文字）
- 管理员 admin / REDACTED-PASSWORD（也可用 13800000000），学生 13800000001~5 / REDACTED-PASSWORD
- users 表新增 username 字段（migration）
- 学生登录/注册页面（Web\AuthController，手机号+密码）
- 管理员登录改为账号输入（支持用户名或手机号）
- 卖书页面 /sell（需登录，未登录跳 /login）
- 应用语言 zh-CN + 中文验证消息
- 全部页面 UI 美化：前台蓝色导航栏+紫色 banner，后台深色侧边栏
- 页脚加管理后台入口

- 书籍详情页 `/books/{id}`（封面大图+实拍缩略图可切换、信息、价格、购买按钮、卖家信息）
- 购买流程：确认购买页 `/buy/{bookId}`（选取书地点+下单须知）→ 下单 → 订单详情（支付/取消）
- 我的订单 `/orders`（列表 + 详情含状态标签/订单轨迹时间线）
- 我的卖书 `/my-sells`（列表含审核状态）
- 导航栏登录后显示"我的订单""我的卖书"
- "管理后台"入口移至登录页底部，全局页脚移除
- **全校 UI 美化**：24个 Blade 文件统一设计系统
  - 色彩：深蓝`#1e3a5f`+琥珀`#c7915c`+暖白`#faf8f5`+墨色`#1a1f2b`
  - 前台：深蓝毛玻璃导航（backdrop-filter）、暖白纸质底+SVG噪声纹理、书籍卡片6px hover抬起+琥珀边框发光、表单琥珀聚焦、琥珀价格/CTA按钮
  - 后台：深海军蓝侧边栏+琥珀激活态、暖白卡片/表格、统计卡片琥珀顶线、全圆药丸徽章、表行交错淡入动画
  - 全局旧色清理：`grep` 验证零残留（#1a73e8/#ff7b25/#e8710a 等全部替换）
  - 新增工具类：`.btn-amber` `.btn-ghost` `.surface-warm` `.divider-amber` `.accent-dot` `.card-accent`

- **Vue 3 SPA 前端开发（Vite 5 + Pinia + Vue Router 4）**
  - 项目脚手架：package.json、vite.config.js（:3000 proxy → :8000）、index.html、main.js、App.vue
  - CSS 设计 token（`tokens.css`，与 Blade 端完全一致）+ localStorage 工具
  - API 层：axios 实例 + 8 模块（auth/books/orders/categories/colleges/wants/upload）
  - 路由：Vue Router 4，14 页面路由 + beforeEach auth/guest 守卫
  - 状态管理：Pinia auth store（token/user 持久化）+ cart store（localStorage 购物车）
  - 布局：MainLayout + AppHeader（深蓝毛玻璃导航，登录态自适应）+ AppFooter
  - 全局组件 8 个：AppLoading/AppEmpty/AppError/StatusBadge/PriceTag/RatingStar/ConfirmModal/Pagination
  - 页面 14 个：Login/Register/Home/BookDetail/Cart/Checkout/Orders/OrderDetail/MyBooks/SubmitBook/Wants/WantDetail/PostWant/Profile
  - 构建验证：Vite build 137 modules，591ms，~158KB JS（gzip 62KB），零错误
  - 后端路由标注：web.php 中 Blade 路由标注 Phase 标记以备逐步替换

### 待办事项
- [ ] MySQL 启动（小皮）+ 前后端联调
- [ ] uni-app 移动端开发

## 2026-05-18

### 今日完成
- 需求文档定稿（平台自营模式），输出 .docx
- 项目目录初始化（`校园二手书/`）
- 设计文档编写完成并通过一轮修订：
  - `docs/DESIGN-SPEC.md` — 数据库 11 表 + ER 关系 + **书籍生命周期** + 订单状态机 + 定价算法 + 卖家结算
  - `docs/API-SPEC.md` — 5 组 API 27 接口，含学生提交卖书 + 平台审核流程
  - `docs/CODING-STANDARDS.md` — PHP/JS/DB/安全/API 格式规范
  - `docs/REQUIREMENT.md` — 需求摘要
- Laravel 项目已创建（`backend/`）
- 关键设计修正：
  - 卖书流程改为学生拍照上传 → 平台审核接单 → 线下拿书 → 入库上架
  - 书籍状态细化：pending_review → approved → active → sold
  - 卖家信息保护：仅购买后可查看
  - 卖家结算标记（seller_paid）

- 前端页面设计文档完成（`docs/FRONTEND-DESIGN.md`），涵盖三端页面/路由/组件树/布局/开发顺序
- 前端设计最终版：新增 5-6 章（关键流程时序图 + 图片存储方案），Vue Web 13页逐页设计，uni-app 15页逐页设计，Admin 8模块逐页设计
- 后端架构设计完成（`docs/BACKEND-DESIGN.md`）
- 后端代码全部完成：
  - 基础设施：ApiResponse Trait + ApiAuth/AdminAuth 中间件 + Kernel
  - 13 个 Model（完整关联 + scope）
  - 4 个 Service（Book/Order/Want/Review）+ BusinessException
  - 7 个 Api Controller（Auth/Book/Order/Want/Category/College/Upload）
  - 9 个 Admin Controller（Auth/Dashboard/Review/Book/Order/User/Want/Category/College）
  - 1 个 Command（超时取消）+ Kernel schedule 注册
  - 14 个 Admin Blade 视图（layout/login/dashboard + 7 模块）
  - 路由完整注册（api.php + web.php）

### 待办事项
- [ ] MySQL 启动（小皮）+ 建库 `campus_books` + 运行 migration + seeder
- [ ] 测试后端可用（注册/登录/提交卖书）
- [ ] 书籍模块（提交卖书 + 平台审核 + 公开列表/详情）
- [ ] 订单模块（下单/支付/取书 + 超时取消）
- [ ] 求购广场模块
- [ ] 评价系统
- [ ] Vue Web 前端开发
- [ ] uni-app 移动端开发

### 遇到的问题
- docx 文档 styles.xml 重复样式导致 Word 报错 → 已重新生成修复
- MySQL 用小皮（phpstudy），已安装未启动，DBeaver 管理
