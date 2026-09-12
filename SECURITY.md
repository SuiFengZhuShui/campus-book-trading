# 安全说明

本仓库启用了 GitHub Dependabot 依赖扫描。当前 **28 条告警已全部标记为 ignored**，
原因记录如下。

## 告警清单

### composer（21 条）—— 后端 `backend/`

| 包 | 当前版本 | 修复所需版本 |
|----|---------|-------------|
| `laravel/framework` | 5.8.38 | 最高需 **12.61.1** |
| `symfony/http-foundation` | 4.4.49 | 5.4.50 |
| `symfony/routing` | 4.4.44 | 5.4.52 |
| `symfony/process` | 4.4.44 | 5.4.51 |
| `symfony/mime` | 4.4.x | 5.4.x |
| `symfony/polyfill-intl-idn` | 1.2x | 1.30.x |
| `psy/psysh` | 0.9.12 | 0.11.23 |
| `phpunit/phpunit` | 8.5.x | 9.x |

### npm（7 条）—— 小程序 `mobile/`

| 包 | 来源 |
|----|------|
| `sharp` | `mobile/package-lock.json`（uni-app 构建工具链） |
| `postcss` | 同上 |
| `nanoid` | 同上 |
| `vue` | 同上 |

这四项都是 **HBuilderX 构建小程序时的工具链依赖**，不进入小程序运行时产物。

## 为什么没有修复

本项目刻意固定在 **Laravel 5.8 + PHP 7.1.3** 这套技术栈上（课程 / 学习阶段的技术选型）。
这些告警**无法通过升级依赖版本来消除**：

- Laravel 自身的告警最高要求 **12.61.1** —— 意味着要跨 5 → 6 → … → 12 共 7 个大版本，
  属重写级别改造；
- Symfony 组件的修复版本是 **5.4.x**，而 Laravel 5.8 把 `symfony/*` 锁在 `^4.3`，装不上 5.4；
  且 Symfony 4.4 已结束维护，官方不再回移补丁；
- `psy/psysh` 的修复版本要求 **PHP ≥ 8.0**，同时 `laravel/tinker ^1.0` 把 psysh 锁在 `^0.9`；
- `phpunit 8.x` 的修复版本要求 PHP 7.3+ 的新特性，与 `phpunit ^8.5` 的约束冲突。

**结论：只有把整套栈升级到 Laravel 12 + PHP 8.2 才能清零这些告警。**

## 已经实际清掉的部分

排查中发现 `backend/` 下有一整套 **Laravel 默认的前端构建脚手架从未被使用**：

- 全部视图的 CSS/JS 走 CDN（bootcdn 的 bootstrap / jquery）与内联 `<style>` / `<script>`；
- 全仓库对 `mix()` / `elixir()` / `app.css` / `app.js` / `laravel-mix` 的引用数为 **0**；
- `resources/js/components/ExampleComponent.vue` 是 Laravel 自带的示例组件。

因此移除了 `package.json`、`webpack.mix.js`、`resources/js/`、`resources/sass/`
及对应的编译产物 `public/js/app.js`、`public/css/app.css`，
**一次性清掉了该 `package.json` 报告的 20 条 axios 告警**（原 48 → 28）。
`public/js/echarts.min.js` 因后台仪表盘有实际引用而保留。

## 风险评估与处置

本项目是**个人学习作品**，不对外提供服务、不处理真实用户数据、未部署到公网。
上述告警的实际风险面有限。

**处置方式**：28 条全部标记为 *ignored*。这不是「无风险」的结论，
而是「在当前项目定位下选择不修复，并记录原因」。

> **如果你要基于本仓库对外部署**，请在部署前完成 Laravel 主版本升级，
> 或自行评估这些告警在你部署形态下的实际可达性。

## 已实施的安全措施

- **密码存储** — 全部经 `bcrypt` 哈希；演示账号密码不硬编码在代码里，
  由 seeder 通过 `DemoPassword::get()` 读取 `.env` 的 `SEED_PASSWORD`，
  未配置时直接报错而非静默使用空密码。
- **接口鉴权** — 移动端 API 走 `api.auth`（`App\Http\Middleware\ApiAuth`）校验 Token；
  管理后台走 `admin.auth`（`App\Http\Middleware\AdminAuth`），两层独立。
- **上传校验** — 图片上传经 `required|image|mimes:jpeg,png,jpg,webp|max:5120`
  校验类型与体积，不做后缀名信任。
- **CSRF** — 沿用 Laravel 默认的 `VerifyCsrfToken` 中间件保护全部写操作表单。
- **数据库** — 数据访问经 Eloquent / Query Builder 的参数绑定，无字符串拼接 SQL。
- **无支付凭据** — 订单支付为站内状态流转（`OrderService::pay()`），
  未接入任何第三方支付网关，不存在支付密钥泄露面。

## 报告安全问题

本项目为个人作品，如发现问题欢迎直接提 Issue。
