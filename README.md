<p align="center">
  <img src="mobile/static/icons/icon-256x256.png" width="120" height="120" style="border-radius: 20px;" alt="校园二手书交易平台">
</p>

<h1 align="center">校园二手书交易平台</h1>

<p align="center">面向高校教材的二手书交易平台 · PC 网页端 + 移动端（微信小程序 / Android App）</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-%E2%89%A57.1.3-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP >= 7.1.3">
  <img src="https://img.shields.io/badge/Laravel-5.8-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 5.8">
  <img src="https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL 5.7+">
  <img src="https://img.shields.io/badge/MiniProgram-uni--app-07C160?style=flat-square&logo=wechat&logoColor=white" alt="uni-app 微信小程序">
  <img src="https://img.shields.io/badge/Android-APK-3DDC84?style=flat-square&logo=android&logoColor=white" alt="Android APK">
  <img src="https://img.shields.io/badge/License-All%20Rights%20Reserved-lightgrey?style=flat-square" alt="License">
</p>

## 项目简介

校园二手教材交易平台。学生把用完的教材提交给平台，经审核后上架；买家按「学院 → 专业 → 课程」找到自己教材，下单并选定校园取书点，支付后获取卖家联系方式（手机号脱敏），线下自提完成交付，交易后可互相评价。买不到的书可以发求购，手上有货的同学直接对接。

一套 uni-app 代码同时构建**微信小程序**和 **Android App**，另有完整的 **PC 网页端**（用户前台 + 管理后台），三端共用同一套 REST API。

> **关于支付**：订单支付为**站内状态流转**（`OrderService::pay()` 将订单由待支付置为已支付并写入时间线），**未接入第三方支付网关**。如需真实收款，需自行对接支付渠道。

## 功能简介

- **三端覆盖** — PC 网页端（用户前台 + 管理后台）、微信小程序、Android App，移动端一套 uni-app 代码三平台构建（`mp-weixin` / `app-plus` / `h5`）。
- **教材维度导航** — 书籍按「学院 → 专业 → 课程」三级组织并叠加图书分类，学生能精确找到本专业本课程要用的教材。
- **卖书流程** — 学生提交卖书信息（书名 / 作者 / 出版社 / ISBN / 所属学院 / 课程 / 成色 / 图片 / 原价与售价），平台审核通过后才上架，被拒会给出原因。
- **买书流程** — 浏览、分类筛选、关键词搜索 → 加购或直接下单 → 从 **6 个预设校园取书点**中选定一个（图书馆一楼大厅 / 一食堂门口 / 二食堂门口 / 教学楼 A 区大厅 / 教学楼 B 区大厅 / 学生活动中心）→ 支付 → 支付后可见卖家姓名与脱敏手机号，线下交接。
- **订单时间线** — 订单状态完整流转（待支付 / 已支付 / 已确认 / 已自提 / 已完成 / 已取消），每步写入 `OrderTimeline`，取消时记录原因。
- **求购广场** — 发布求购需求，有书的同学可对接求购直接提交卖书，表单自动带出求购信息。
- **评价与评论** — 订单完成后买家可评价，评价与评论在后台可管理。
- **管理后台** — 书籍审核、图书分类、学院 / 专业 / 课程维护、订单管理、评价管理、评论管理、求购管理、用户管理、数据看板（4 项指标）。
- **接口规范** — 统一响应格式 `{code, message, data}`，小程序与 App 共用同一套 API。

## 技术栈

| 端 | 技术 |
|----|------|
| 后端 | Laravel 5.8 + MySQL 5.7+ |
| API | RESTful + Token 认证（`routes/api.php`，与 `routes/web.php` 双路由） |
| PC 网页端 | Laravel Blade + Bootstrap + 原生 JavaScript（用户前台 `views/web` + 后台 `views/admin`） |
| 移动端 | uni-app（Vue 2 语法）— 微信小程序 / Android App / H5 三平台同源 |
| App 打包 | HBuilderX 原生 App 云打包（Android） |

## 快速体验

> 没有线上演示环境，本地按下面步骤即可跑起来。

### 环境要求

- PHP ≥ 7.1.3，需开启 `mbstring`、`openssl`、`pdo_mysql` 扩展
- MySQL 5.7+
- Composer
- HBuilderX（运行 / 打包移动端）
- 微信开发者工具（调试小程序）

### 后端 + PC 网页端启动

```bash
cd backend
composer install
cp .env.example .env       # 配置数据库连接、微信 appid/secret
php artisan key:generate
php artisan migrate --seed # 建表并填充测试数据
php artisan storage:link   # 书籍图片上传需要，必执行
php artisan serve
```

启动后 PC 网页端首页即 `http://127.0.0.1:8000`，管理后台在 `/admin`。

### 测试账号

| 角色 | 账号 | 密码 |
|------|------|------|
| 管理员 | `admin`（手机号 `13800000000`） | 见 `.env` 的 `SEED_PASSWORD` |
| 学生 | 手机号 `13800000001` ~ `13800000015`（共 15 个） | 见 `.env` 的 `SEED_PASSWORD` |

> 演示账号密码**不硬编码在代码里**，seeder 通过 `DemoPassword::get()` 读取 `.env` 的 `SEED_PASSWORD`（`.env.example` 默认 `ChangeMe@2026`）。未配置该变量时 seeder 会直接报错，不会静默使用空密码。
> **生产环境务必改成强密码**，并在 seed 之前就设好。

### 移动端启动

1. HBuilderX 打开 `mobile/` 目录
2. `manifest.json` 中配置微信小程序 appid（打包 App 还需配置图标与启动图）
3. 修改 `mobile/utils/request.js` 内的 API 地址，指向本地后端
4. 运行到微信开发者工具，或用 HBuilderX 内置浏览器预览 H5

### 打包 Android APK

HBuilderX → **发行** → **原生App-云打包** → 选 Android → 打包方式选「快速安心打包」，产物输出到 `mobile/unpackage/release/apk/`。

打包前务必检查三项（详见 `mobile/CLAUDE.md`）：

1. `manifest.json` 图标/启动图层级 —— 图标在 `distribute.icons.android`，**不是** `distribute.android.icons`；启动图在 `distribute.splashscreen.android`
2. `utils/request.js` 的 `#ifdef APP-PLUS` 分支 BASE_URL 必须指向手机可访问的地址（局域网 IP 或公网域名），不能是 `127.0.0.1`
3. App WebView 与 H5 渲染有差异 —— `<text>` 默认 inline，独立文案/按钮要显式 `display:block`；`white-space:nowrap` 要直接写在 `<text>` 上，不继承父级

## 安装教程

生产环境部署只需要注意一件事：**Web 根目录必须指向 `backend/public`，不能指向项目根目录**。Laravel 的 `.env`、`storage/`、`vendor/` 都不在 `public` 下，根目录暴露会造成配置泄露。

`deploy/nginx.conf` 里有一份完整的 HTTPS 生产配置可直接参考（含证书、安全头与 CSP），下面是最小的通用版本。

### 伪静态配置（Nginx）

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/backend/public;   # 必须指向 public
    index index.php;

    # 拒绝隐藏文件（保留 ACME 验证目录）
    location ~ /\.(?!well-known).* { deny all; }

    # 拒绝敏感后缀
    location ~* \.(env|log|sql|sqlite|db|bak|old|save|swp|tmp|ini|lock)$ { deny all; }
    location ~* (composer\.(json|lock)|package(-lock)?\.json)$ { deny all; }
    location ~ ^/(vendor|node_modules)/ { deny all; }

    # 上传文件（执行 storage:link 后对外可见）
    location /storage/ {
        alias /var/www/backend/storage/app/public/;
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        # 按实际 PHP 版本调整 socket 路径
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 伪静态配置（IIS）

在 `backend/public/` 目录放 `web.config`：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
  <system.webServer>
    <rewrite>
      <rules>
        <rule name="Laravel" stopProcessing="true">
          <match url="^(.*)$" />
          <conditions logicalGrouping="MatchAll">
            <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
          </conditions>
          <action type="Rewrite" url="index.php/{R:1}" />
        </rule>
      </rules>
    </rewrite>
  </system.webServer>
</configuration>
```

### Apache

`backend/public/.htaccess` 已内置，确认 `mod_rewrite` 已开启即可，无需额外配置。

### 部署后必做

```bash
php artisan storage:link       # 建立 storage 软链接，否则书籍图片无法访问
php artisan config:cache       # 缓存配置（改 .env 后需 config:clear 再重新 cache）
php artisan route:cache
php artisan migrate --force    # 生产环境迁移需加 --force
```

移动端上线前，记得把 `mobile/utils/request.js` 的 API 地址改成生产域名后重新打包。

## 项目结构

```
├── backend/              # Laravel 后端（PC 网页端 + 管理后台 + API）
│   ├── app/              # 模型（Book/Order/Want/College/Major/Course…）与控制器
│   │   └── Http/Controllers/
│   │       ├── Web/      # PC 网页端前台
│   │       ├── Admin/    # 管理后台
│   │       └── Api/      # 移动端 API（小程序 + App 共用）
│   ├── database/         # 迁移与种子数据
│   ├── resources/views/  # Blade 视图（web 前台 / admin 后台）
│   └── routes/           # web.php + api.php 双路由
├── mobile/               # uni-app 移动端（小程序 / App / H5 同源）
│   ├── pages/            # auth 登录 / books 书籍 / buy 购买 / cart 购物车
│   │                     # orders 订单 / wants 求购 / my-sells 我的卖书 / user 个人中心
│   ├── utils/request.js  # 请求封装（含 APP-PLUS 条件编译分支）
│   ├── manifest.json     # 各平台构建配置（含 App 图标 / 启动图）
│   └── CLAUDE.md         # 移动端开发约定与 APK 打包说明
├── deploy/               # 部署配置（nginx.conf / checklist.md）
├── docs/                 # 设计文档（需求 / 后端设计 / API 规范 / 前端设计）
├── DEVLOG.md             # 开发日志
└── CLAUDE.md             # 项目约定与导航图
```

## 测试

```bash
cd backend && ./vendor/bin/phpunit
```

## 文档

- `校园二手书平台-使用手册.docx` — 用户操作手册
- `校园二手书平台-需求文档.docx` — 需求说明
- `docs/BACKEND-DESIGN.md` — 后端设计文档
- `docs/API-SPEC.md` — 接口规范
- `mobile/CLAUDE.md` — 移动端开发约定与 APK 打包流程
- `DEVLOG.md` — 开发日志

## 声明

> 本项目为个人学习与实践作品，著作权归作者所有。
>
> 源码仅供学习、研究与技术交流使用。未经授权，不得用于任何商业用途，包括但不限于搭建线上平台对外经营、提供付费服务、二次销售或整体/部分纳入商业产品。
>
> 使用者应自行遵守所在国家或地区的法律法规。因使用本项目产生的一切后果，由使用者自行承担。
>
> 使用本项目即表示您已充分理解并同意本声明的全部内容。

---

Copyright © 2026 随风逐水。保留所有权利。
