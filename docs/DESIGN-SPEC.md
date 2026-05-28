# 设计规格

## 系统架构

```
┌──────────┐  ┌──────────┐  ┌──────────┐
│   Web 前端 │  │   uni-app │  │  管理后台  │
│   (Vue)   │  │  (移动端)  │  │ (Blade)   │
└────┬─────┘  └────┬─────┘  └────┬─────┘
     │             │             │
     └──────────┬──┘             │
                │                │
           ┌────▼────────────────▼──┐
           │    Laravel API + Web    │
           │    (Token 认证)          │
           └───────────┬────────────┘
                       │
                  ┌────▼────┐
                  │  MySQL   │
                  └─────────┘
```

## 路由规划

- `/api/*` — API 路由，Token 认证（Vue / uni-app 调用）
- `/admin/*` — 管理后台，Session 认证 + Blade 视图
- `/*` — Vue SPA 入口

---

## 核心流程

### 卖书流程（学生 → 平台）

```
学生拍照上传 → 平台审核 → 审核通过(接单) → 平台线下拿书 → 确认入库 → 上架
```

1. 学生提交：拍好照片，填写书名/作者/出版社/原价/成色，提交给平台
2. 平台审核：管理员后台审核，可调整信息，通过即"接单"
3. 线下拿书：平台按约定时间地点取书
4. 确认入库：拿书后确认入库，书籍状态变为"在售"
5. 卖出结算：书籍被买走、订单完成后，卖书学生进账（cost_price）

### 买书流程

```
浏览/搜索 → 加入购物车 → 提交订单 → 模拟支付 → 平台确认 → 线下取书 → 确认取书 → 评价 → 完成
```

### 书籍生命周期

```
pending_review ──审核通过──→ approved ──拿书入库──→ active ──售出──→ sold
     │                          │
     └──审核驳回──→ removed      └──超时/拒收──→ removed
```

---

## 数据库设计

### 1. users — 用户表

| 字段 | 类型 | 约束 | 说明 |
|------|------|------|------|
| id | bigint unsigned | PK, auto_increment | |
| student_id | varchar(20) | unique, nullable | 学号，管理员为空 |
| name | varchar(50) | NOT NULL | 真实姓名 |
| phone | varchar(11) | unique, NOT NULL | 手机号，登录用 |
| password | varchar(255) | NOT NULL | bcrypt |
| role | enum('student','admin') | NOT NULL, default 'student' | |
| avatar | varchar(255) | nullable | 头像 |
| status | tinyint | default 1 | 1=正常 0=禁用 |
| created_at | timestamp | | |
| updated_at | timestamp | | |

### 2. categories — 书籍分类

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| name | varchar(50) | 教材教辅/考研考公/文学小说/杂志漫画/其他 |
| sort | int | 排序 |
| created_at/updated_at | timestamp | |

### 3. colleges / majors / courses — 学院/专业/课程

**colleges**: id, name, sort, created_at, updated_at

**majors**: id, college_id(FK), name, sort, created_at, updated_at

**courses**: id, major_id(FK), name, sort, created_at, updated_at

### 4. books — 书籍（每行 = 一本实物书，贯穿完整生命周期）

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| title | varchar(200) | 书名 |
| author | varchar(100) | 作者 |
| publisher | varchar(100) | 出版社 |
| isbn | varchar(20) | nullable |
| category_id | bigint unsigned FK | 分类 |
| course_id | bigint unsigned FK, nullable | 关联课程 |
| condition | enum('like_new','excellent','good','fair') | 成色（学生提交，平台可改） |
| original_price | decimal(10,2) | 原价 |
| price | decimal(10,2) | 售价（平台审核时定） |
| cost_price | decimal(10,2) | 收书价（平台付给卖书学生的） |
| seller_id | bigint unsigned FK | 卖书学生 |
| status | enum('pending_review','approved','active','sold','removed') | 见生命周期图 |
| submitted_at | timestamp | 学生提交时间 |
| approved_at | timestamp, nullable | 平台审核通过时间 |
| received_at | timestamp, nullable | 平台拿书入库时间 |
| seller_paid | tinyint | default 0，订单完成后置 1（卖家进账） |
| description | text, nullable | |
| reject_reason | varchar(255), nullable | 驳回原因 |
| created_at/updated_at | timestamp | |

**时间戳区分**：同一书名+作者+出版社的多本书，通过 `created_at` / `submitted_at` 区分先后。

### 5. book_images — 书籍图片

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| book_id | bigint unsigned FK | |
| path | varchar(255) | 图片路径 |
| type | enum('cover','inner','spine','other') | |
| sort | int | 排序 |
| created_at | timestamp | |

### 6. orders — 订单

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| order_no | varchar(32) | 订单号，唯一 |
| buyer_id | bigint unsigned FK | 买家 |
| total_amount | decimal(10,2) | 订单金额 |
| status | enum('pending','paid','confirmed','picked_up','completed','cancelled') | |
| pickup_location | varchar(200) | 取书地点 |
| pickup_time | datetime, nullable | 约定取书时间 |
| paid_at | timestamp, nullable | |
| confirmed_at | timestamp, nullable | |
| picked_up_at | timestamp, nullable | |
| completed_at | timestamp, nullable | |
| cancelled_at | timestamp, nullable | |
| cancel_reason | varchar(255), nullable | |
| created_at/updated_at | timestamp | |

### 7. order_items — 订单明细

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| order_id | bigint unsigned FK | |
| book_id | bigint unsigned FK | |
| price | decimal(10,2) | 下单时价格快照 |
| created_at | timestamp | |

### 8. order_timeline — 订单时间线

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| order_id | bigint unsigned FK | |
| status | varchar(30) | 状态名 |
| remark | varchar(255), nullable | |
| created_at | timestamp | |

### 9. reviews — 评价

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| order_id | bigint unsigned FK, unique | 一个订单一条评价 |
| user_id | bigint unsigned FK | 评价人（买家） |
| book_id | bigint unsigned FK | |
| book_rating | tinyint unsigned | 书况 1-5 |
| service_rating | tinyint unsigned | 服务 1-5 |
| comment | varchar(500), nullable | |
| created_at/updated_at | timestamp | |

### 10. wants — 求购

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| user_id | bigint unsigned FK | 发布者 |
| title | varchar(200) | |
| author | varchar(100), nullable | |
| publisher | varchar(100), nullable | |
| max_price | decimal(10,2) | 最高可接受价格 |
| acceptable_condition | varchar(50), nullable | 可接受成色（逗号分隔） |
| status | enum('active','fulfilled','expired','closed') | default 'active' |
| expires_at | timestamp | 发布后 7 天 |
| created_at/updated_at | timestamp | |

### 11. want_fulfillments — 求购接单

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned PK | |
| want_id | bigint unsigned FK | |
| fulfiller_id | bigint unsigned FK | 接单人（有书的学生） |
| book_id | bigint unsigned FK, nullable | 上架后关联 |
| status | enum('pending','listed','completed') | |
| created_at/updated_at | timestamp | |

### ER 关系

```
users 1──N books (seller_id)
users 1──N orders (buyer_id)
users 1──N reviews
users 1──N wants
users 1──N want_fulfillments (fulfiller_id)
books 1──N book_images
books 1──N order_items
books 1──N reviews
categories 1──N books
courses 1──N books
colleges 1──N majors 1──N courses
orders 1──N order_items
orders 1──N order_timeline
orders 1──1 reviews
wants 1──N want_fulfillments
```

---

## 订单状态机

```
pending ──pay──→ paid ──admin_confirm──→ confirmed
  │                │                          │
  └──cancel──→  cancelled    ┌──cancel──→  cancelled
                             │
                    confirmed ──buyer_pickup──→ picked_up
                                                   │
                                    picked_up ──review──→ completed
```

## 权限规则

- **卖家信息保护**：书籍的 seller 信息仅在该书被购买后，对购买者可见（防止绕过平台私下交易）
- **求购同理**：接单人信息仅在求购完成后对发布者可见

## 超时规则

- 待付款 30 分钟未支付 → 自动取消，书籍恢复 active
- 已付款 48 小时未确认 → 自动取消，书籍恢复 active
- 通过 Laravel 计划任务每分钟执行检查

## 定价建议

1. 搜索 books 表中同 title + author + publisher 的 active 书籍
2. 取已有书籍的平均价格作为建议基准价
3. 无同类书籍则用 original_price × 0.5 作为建议价
4. 平台审核时手动定最终售价和收书价

## 卖家结算

- 书籍售出后，订单完成（status=completed）时，`books.seller_paid` 置 1
- 表示平台已向卖书学生支付 cost_price
- 实际支付线下完成，系统只做状态标记
