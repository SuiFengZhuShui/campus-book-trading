# 校园二手书交易平台

面向校园的二手书买卖平台，学生可以发布闲置书籍、浏览求购信息、在线下单交易。Web 后台管理 + 微信小程序双端。

## 功能特性

- **书籍买卖** — 发布二手书（书名、价格、成色、图片），浏览与搜索在售书籍
- **求购广场** — 发布求购需求，卖家可对接求购订单
- **购物车与订单** — 加购、下单、订单状态流转（待支付/待发货/待收货/完成）
- **我的卖书** — 卖家管理在售书籍与已售订单
- **用户体系** — 微信小程序登录、学生身份、个人中心
- **后台管理** — 管理员审核书籍、管理用户与订单

## 技术栈

| 端 | 技术 |
|----|------|
| 后端 | Laravel 5.8 + MySQL |
| 小程序端 | uni-app（Vue 2 语法）+ 微信小程序 |
| Web 后台 | Laravel Blade + Bootstrap + JavaScript |

## 快速开始

### 环境要求

- PHP ≥ 7.1.3（含 mbstring、openssl、pdo_mysql 扩展）
- MySQL 5.7+
- Composer
- HBuilderX（小程序端运行）
- 微信开发者工具

### 后端启动

```bash
cd backend
composer install
cp .env.example .env        # 配置数据库连接、微信 appid/secret
php artisan key:generate
php artisan migrate --seed   # 建表并填充测试数据
php artisan serve
```

### 小程序端启动

1. HBuilderX 打开 `mobile/` 目录
2. `manifest.json` 中配置微信小程序 appid
3. 修改 `mobile/` 内 API 请求地址指向本地后端
4. 运行到微信开发者工具

> 测试账号：管理员 `admin`，学生 `13800000001` ~ `13800000003`（密码见 `.env` 配置）

## 项目结构

```
├── backend/          # Laravel 后端（API + Web 后台）
│   ├── app/          # 模型 / 控制器 / 中间件
│   ├── database/     # 迁移与种子数据
│   └── routes/       # web + api 路由
├── mobile/           # uni-app 微信小程序
│   └── pages/        # auth 登录 / books 书籍 / buy 购买 / cart 购物车
│                     # orders 订单 / wants 求购 / my-sells 我的卖书 / user 个人中心
├── deploy/           # 部署相关
├── docs/             # 设计文档（后端设计 / 需求 / 计划）
└── CLAUDE.md         # 项目约定与导航图
```

## 测试

```bash
cd backend && ./vendor/bin/phpunit
```

## 文档

- `校园二手书平台-使用手册.docx` — 用户操作手册
- `校园二手书平台-需求文档.docx` — 需求说明
- `docs/BACKEND-DESIGN.md` — 后端设计文档
- `DEVLOG.md` — 开发日志

## License

Copyright © 2026 随风逐水。保留所有权利。
