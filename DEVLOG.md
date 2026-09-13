# 开发日志

## 2026-06-18

### 换机部署文档 + PPT 截图脚本重构

- 完成 `换机注意事项.txt`：U 盘复制项目到机房后的完整部署步骤
  - 本次新增 3 个问题：PHP PATH 未配置、.htaccess 为空、小皮伪静态 Nginx 规则冲突
- `rebuild_v8.py` 拆分为纯截图脚本 `capture_screenshots.py`
  - 去掉 PPT 拼装逻辑（交给 ppt-master / document-skills）
  - ID 改为数据库动态查询，不再硬编码
  - Web 14 页 + Admin 14 页自动登录截图，2560×1440 全横屏

## 2026-06-06

### 今日完成
- 求购接单全链路：接单→卖书提交→审批通过→自动标记求购 fulfilled
- want_id 贯穿 fulfill→sell→submit→approve 四步，BookService 自动关联 WantFulfillment
- 双端"我要卖这本书"集成接单 API，已接单显示提示"你已卖当前求购的书"
- 自己不能买自己的书（双端+API下单拦截）
- 书详情页图片轮播：左右箭头+缩略图+计数器
- 字体统一简体：LXGW WenKai（去繁体 TC）+ body 回退链去英文衬线体
- 搜索全站 AJAX 化：首页+求购搜索无刷新，背景动画不中断
- 求购广场独立搜索框+清除按钮，全局搜索框/学院下拉在求购页隐藏
- 求购 API 修复：列表过滤 closed 状态、书籍列表/详情加 seller_id
- 401 拦截自动跳转登录页
- 卖书页上传预览修复（accept 属性）、提交成功 toast/清空修复
- 测试数据整理：每人每种状态约2条，老八清空
- 书籍图片全部替换为用户指定图片

### 待办事项
- [ ] 无

### 遇到的问题
- seek 页面图片 PNG 内容存到 .jpg 文件名导致浏览器不识别→GD 转码修复
- 字体 TC 版显示繁体→换简体 LXGW WenKai
- form.reset() 重置到预填值非空值→手动清空各字段
- AJAX 搜索走到 index() 而非 search()→URL 改 /search

## 2026-06-03

### 今日完成
- **图片不显示修复**：`public/storage` Unix symlink 替换为 PowerShell Junction，恢复被清空的 `.htaccess`
- **`rejected` vs `removed` 状态分离**：3 个 admin Blade 页面 badge/label/筛选全部拆开，`rejected`→红色已驳回，`removed`→灰色已下架
- **订单评价逻辑**：平台收购订单（`buyer->role === 'admin'`）不显示/不允许评价，ReviewService/API/Web/Mobile 四端同步
- **订单号格式统一**：Seeder 改用 `YmdHis+4位随机数` 匹配 `OrderService::generateOrderNo()`，清理旧 ORD 格式数据
- **软删除记录物理删除**：6 条软删除+17 条关联记录全部硬删除
- **移动端首页加卖书按钮**：右下角浮动按钮跳转 `/pages/books/sell`
- **Web 首页分页**：`paginate(50)`→`paginate(20)`，测试数据 50→100 本，5 页分页
- **Web 书籍详情加返回按钮**：`history.back()` + 香槟金渐变
- **Layout 搜索框上下文**：求购页隐藏学院下拉和搜索栏，避免误触跳首页
- **Admin 书籍管理按钮**：统一显示「编辑」，「查看」→「编辑」，`rejected` 增加「删除」按钮
- **后台菜单**：隐藏冗余的「院校管理」，保留「分类管理」
- **CLAUDE.md**：新增 7 条铁律（#55~#61）

## 2026-05-30

### 今日完成
- **数据库引擎 InnoDB 修复**：`database.php` `'engine' => null` → `'engine' => 'InnoDB'`，14 张表 ALTER 转 InnoDB，dump 文件全局替换 ENGINE=MyISAM → InnoDB
- **移动端我的卖书 - 列表去封面**：列表不再显示封面图，点进详情才能看图片
- **移动端我的卖书 - 卡片点击跳转**：补 `goDetail()` 方法，点击跳转书籍详情
- **价格 null 显示修复（三端）**：待审核书籍 price/cost_price 为 null，前端展示空 `¥` 像 bug
  - 移动端详情 `books/detail.vue`：`v-if="book.price"` + `v-else` 显示"待定价"
  - Blade 列表 `my-sells.blade.php`：`@if($book->price)` 判空
  - Blade 详情 `book-detail.blade.php`：价格 + "省¥" 两处加 `@if($book->price)`
- **API `/api/books/{id}` 扩展**：返回 `status` 和 `is_seller` 字段
- **移动端详情 - 自己的书隐藏购买栏**：`v-if="book.status === 'active' && !book.is_seller"`，待审核/自己的书不显示"立即购买"
- **`canViewSeller()` 修复**：卖家自己也能看到卖家信息卡片
- **Web + Admin 截图**：Playwright 自动化截取 25 张（Web 14 + Admin 11），1440px 全页截图
- **使用手册 v3.1**：docx npm 包程序化生成，文案更新 + 40 截图位
- **源代码文本**：Web 端（PHP + Blade）+ 移动端（Vue + JS）合并为 `source-code.txt`，10165 行
- **管理员密码修正**：实际密码为 `REDACTED-PASSWORD`（非 REDACTED-PASSWORD），旧哈希不匹配已重新 hash
- **旧文件清理**：删除旧 MyISAM dump（防误导入回退）+ 旧 source_code.txt（命名不统一）
- **Web 注册软删除排除**：`unique:users` → `unique:users,phone,NULL,id,deleted_at,NULL`，Web 端注册遗漏软删除排除，导致已删号无法重注册
- **books 表 status ENUM 缺 `rejected`**：migration 枚举值只有 5 个（pending_review/approved/active/sold/removed），`BookService::reject()` 设 `rejected` 被 MySQL 截断报错。修复：ALTER TABLE + migration 补值
- **Web 端评价功能补齐**：之前只有 API + Service，Blade 端三条全漏——`OrderController@review` 方法、`/orders/{id}/review` 路由、订单详情页评价表单。补测试数据才发现
- **跨层完整性检查规则**：新功能加完后三端自查——API 有 ≠ Blade 有 ≠ 移动端有，每层确认 路由 + Controller + UI + 导航入口 齐全才算完成。子页面加完必须回到父页面补入口按钮。
- **评价表单左右布局**：书籍评分在左、服务评分在右并排显示
- **老八用户清理**：物理删除软删除的刘七(13800000005) + 老八(13800000006)，让用户可重新注册
- **学号顺延**：李四→20240000002、王五→20240000003，按张三的 20240000001 顺延
- **users 表唯一索引修复**：phone 和 student_id 的单列唯一索引改为 (column, deleted_at) 复合唯一索引，软删除不阻止重注册
- **移动端下单取书地点**：从自由输入改为 picker 选择器，6 个选项与 Web 端一致（图书馆/一食堂/二食堂/教学楼A区/B区/学生活动中心）
- **移动端求购广场 - 我的求购入口**：右上角新增苔绿胶囊按钮"我的求购 →"，跳转 `/pages/wants/mine`

### 待办事项
- [ ] HBuilder X 运行移动端验证：我的卖书列表/详情价格/购买栏隐藏
- [ ] 移动端截图：微信开发者工具手动截取 16 张

### 遇到的问题
- **数据库全部 MyISAM**：`database.php` `'engine' => null` 走 MySQL 默认引擎（小皮配的 MyISAM），无外键无事务。修复：改配置 + 转换表 + 修正 dump
- **旧 dump 导入导致引擎回退**：转换 InnoDB 后导入旧 MyISAM dump 会覆盖引擎。教训：转换后立即删旧 dump
- **管理员密码不匹配**：文档写 REDACTED-PASSWORD 但数据库哈希实际是 REDACTED-PASSWORD。重置后密码改为 REDACTED-PASSWORD
- **Blade `¥{{ $book->price }}` null 渲染为 `¥`**：PHP null 在字符串中转为空串，看起来像 bug。解决方案：统一加判空，显示"待定价"
- **自己的待审核书底部显示"立即购买"**：详情页未区分书籍状态和卖家身份。解决方案：API 加 `status` + `is_seller`，前端双条件判断
- **Web 注册被已删号阻止**：软删除用户（deleted_at 非空）仍被 unique 验证拦截。根因：Web AuthController 注册验证漏写 `deleted_at,NULL`。修复：补上排除条件
- **驳回书籍报 Data truncated**：books 表 status ENUM 没有 `rejected` 值。根因：migration 写死了 5 个值，后来加的 `rejected` 状态未同步。修复：ALTER TABLE + migration 补值
- **Web 端评价功能从未上线**：API + ReviewService 都有，但 Blade 路由/Controller/UI 全缺。根因：写完 API 就以为是完成，没人端到端走一遍。修复：补全三层 + 测试数据验证

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
- **使用手册更新**：v3.0，40 截图位，15 Web + 16 移动 + 9 后台 + 15 验证点，完整覆盖软著申请所需截图
- **登录 401 错误提示修复**：request.js 401拦截器区分登录/注册接口（业务错误不清理auth）
- **注册 422 中文提示修复**：request.js 提取 validation errors 第一条覆盖顶层英文 message
- **unique 验证排除软删除**：AuthController register/updateProfile 加 `deleted_at,NULL` 条件
- **测试数据物理删除**：清理 12 个软删除用户 + 1 本书（管理者指令=真删除）
- **订单空状态文案优化**：computed emptyText 按 pending/paid/confirmed/picked_up 显示不同文案
- **后端性能优化**：WantController N+1（`withCount('fulfillments')`）、AuthController::me() 4次 SQL→1次 GROUP BY、首页分类+书籍并行加载
- **求购页分页**：加载更多（page/per_page/hasMore）
- **空状态按钮换行**：my-sells/wants/mine/wants-mine 空状态 `<text>` 加 `display:block`
- **求购浮动按钮改为文字**："+"圆形 → "发布求购"胶囊按钮
- **卖书预填修复**：onLoad接收参数+decodeURIComponent+categoryIndex同步picker
- **ISBN → 书号**：卖书页标签改为中文
- **首页卡片布局对齐**：title/author min-height + safe-image外层view包裹固定高度
- **登录/注册满屏不滚动**：pages.json disableScroll + height:100vh+box-sizing
- **跨端同步检查规则**：记忆+feedback（改一端必须同步检查另一端）
- **全项目 Bug 排查**：发现 32 个 bug，修复 26 个（CRITICAL 9 + HIGH 9 + MEDIUM 8）
  - 后端：8 个 Model 补 SoftDeletes + 6 张表迁移补 deleted_at + error() 参数反转×2 + 订单删除加状态校验×3
  - 后端：Want scopeActive 加过期过滤 + BookController eager load 补 seller + badge-cyan CSS 补缺
  - 后端：3 个 admin Blade innerHTML 注入改为 createElement 安全拼接
  - 移动端：8 个页面 catch 块静默吞错补 toast + sell categoryIndex 修复 + edit error 路径修复
  - 移动端：index.html 路径修复 + safe-image fail 回调补日志
- **源代码文本生成**：软著申请用，PHP+Vue+JS 全量源码合并文本

- **数据库引擎 InnoDB 修复**：`database.php` `'engine' => null` → `'engine' => 'InnoDB'`，14 张表 ALTER 转 InnoDB，dump 文件 `ENGINE=MyISAM` → `ENGINE=InnoDB` 全局替换

### 待办事项
- [ ] HBuilder X 运行移动端验证：首页卡片/登录满屏/卖书预填/求购分页/订单空状态

### 遇到的问题
- **数据库全部 MyISAM**：`database.php` `'engine' => null` 走 MySQL 默认引擎（小皮配的 MyISAM），导致无外键、无事务。修复：改配置 + 转换表 + 修正 dump 文件
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
- **WantController N+1 查询**：`fulfillments()->count()` 在 map 内每行一次 SQL。解决：`withCount('fulfillments')` + `fulfillments_count`
- **AuthController::me() 6次SQL**：4个 orderCount + 2个 count。解决：订单统计用 GROUP BY status 一次查询
- **求购预填数据乱码**：uni-app微信小程序 onLoad 不解码 URL 参数。解决：手动 `decodeURIComponent()`
- **safe-image class不生效**：微信小程序自定义组件外部class不透视。解决：外层 `<view class="cover-wrap">` 固定高度包裹
- **登录/注册页可滚动**：100vh含导航栏，min-height超出可视区。解决：pages.json disableScroll + height:100vh

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

## 2026-09-13
### 今日完成
- README 重构：版式对齐参考项目，并按代码校正业务描述（实为 PC 网页端 + 微信小程序 + Android APK 三端；交易是平台审核上架 + 线下自提，非快递；书籍按学院→专业→课程三级组织）
- seeder 演示密码外置：新增 `DemoPassword` 从 `.env` 的 `SEED_PASSWORD` 读取，未配置时抛异常，不再硬编码明文密码
- 合并 GitHub 侧 `.gitignore` 敏感文件防护（`.env.*` / `*.bak*` / `*.sql`）
- 移除从未使用的 Laravel-mix 前端构建脚手架（视图走 CDN + 内联，全仓库对 `mix()` / `app.css` / `app.js` 引用数为 0）；补入漏跟踪的 `public/js/echarts.min.js`
- 新增 `SECURITY.md`：28 条依赖告警的成因、为何无法通过升级依赖消除、已实施的安全措施
- 解决本地源目录与 GitHub 的历史分叉（两侧无共同祖先，此前本地提交推不上去）→ 用 `git merge -s ours` 同步，未 force-push、未毁远端历史
- 摘出作品集不收录的材料（3 个演示 PPTX + 2 个内部笔记），文件保留在磁盘

### 待办事项
- [ ] MySQL 启动（小皮）+ 建库 `campus_books` + 运行 migration + seeder
- [ ] 测试后端可用（注册/登录/提交卖书）
- [ ] 书籍 / 订单 / 求购广场 / 评价 各模块
- [ ] Vue Web 前端开发
- [ ] uni-app 移动端开发

### 遇到的问题
- `backend/public/favicon.ico` 是 0 字节空文件，据此误判「无图标」，实际小程序端有完整图标集 `mobile/static/icons/`（48~1024 共 7 个）→ 已补进 README
- 本地与 GitHub 是两条独立历史（各自 `git init`，根提交 `a6fd2ac` vs `1e3b8d1`，`merge-base` 为空）→ 已解决
