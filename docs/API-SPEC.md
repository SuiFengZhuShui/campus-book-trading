# API 规格

## 认证方式

API 使用 Token 认证，登录后返回 access_token，后续请求 header 携带 `Authorization: Bearer <token>`。

## 通用响应格式

```json
// 成功
{"code": 200, "message": "success", "data": {}}

// 分页
{"code": 200, "message": "success", "data": {"list": [], "meta": {"current_page": 1, "per_page": 15, "total": 100, "last_page": 7}}}

// 验证失败
{"code": 422, "message": "验证失败", "errors": {"phone": ["手机号不能为空"]}}

// 错误
{"code": 4xx/5xx, "message": "错误描述"}
```

---

## 一、认证模块

### POST /api/auth/register — 学号注册

```
Body: student_id*, name*, phone*, password*, password_confirmation*
校验：学号唯一、手机号唯一、密码 ≥ 8 位含大小写字母数字
```

### POST /api/auth/login — 手机号登录

```
Body: phone*, password*
Response: { code: 200, data: { token: "...", user: {...} } }
```

### POST /api/auth/logout — 退出登录

### GET /api/auth/me — 当前用户信息

---

## 二、书籍模块

### 2.1 学生提交卖书

#### POST /api/books/submit — 提交卖书申请

```
Header: Authorization: Bearer <token>
Body: title*, author*, publisher*, isbn, category_id*, course_id, condition*, original_price*, description
      images[]* (至少3张，最多9张)
逻辑：status='pending_review'，submitted_at=now
Response: { code: 200, data: { book: {...} } }
```

#### GET /api/my-books — 我提交的书籍

```
Header: Authorization: Bearer <token>
Query: status(可选), page, per_page
Response: { list: [{id, title, status, status_label, submitted_at, ...}], meta: {...} }
```

### 2.2 公开浏览（无需认证）

#### GET /api/books — 在售书籍列表

```
Query: category_id, condition, keyword(搜索书名/作者), course_id, page, per_page(默认15)
筛选条件：仅 status='active'
Response: { list: [{id, title, author, publisher, cover_img, condition_label, price, original_price, submitted_at}], meta: {...} }
注意：列表中不返回 seller 信息
```

#### GET /api/books/{id} — 书籍详情

```
Response: { book: {..., images: [...], reviews: [...]} }
注意：仅当请求者为该书购买者时，才返回 seller 信息（姓名/手机号）
```

#### GET /api/categories — 分类列表

#### GET /api/colleges — 学院列表

#### GET /api/colleges/{id}/majors — 专业列表

#### GET /api/majors/{id}/courses — 课程列表

---

## 三、订单模块（需认证）

### POST /api/orders — 创建订单

```
Header: Authorization: Bearer <token>
Body: book_ids[]*, pickup_location*
逻辑：检查库存(均为active)、计算总价、生成订单号、status='pending'
Response: { code: 200, data: { order: {...} } }
```

### GET /api/orders — 我的订单

```
Query: status(可选筛选), page, per_page
Response: { list: [{id, order_no, status, total_amount, items, created_at}], meta: {...} }
```

### GET /api/orders/{id} — 订单详情

```
Response: { order: {..., items: [...], timeline: [...], seller: {...}} }
注意：订单详情中返回 seller 信息（仅购买者可查看）
```

### POST /api/orders/{id}/pay — 模拟支付

```
逻辑：状态必须='pending' → 改为'paid'，记录 paid_at
```

### POST /api/orders/{id}/cancel — 取消订单

```
逻辑：状态='pending'或'paid' → 改为'cancelled'，恢复书籍 active
Body: reason(选填)
```

### POST /api/orders/{id}/pickup — 确认取书

```
逻辑：状态必须='confirmed' → 改为'picked_up'
```

### POST /api/orders/{id}/review — 评价

```
Body: book_rating*(1-5), service_rating*(1-5), comment(选填)
逻辑：状态必须='picked_up' → 创建 review → 改为'completed' → seller_paid 置 1
```

---

## 四、求购模块（需认证）

### GET /api/wants — 求购列表

```
Query: keyword, status, page, per_page
Response: { list: [{id, title, author, max_price, status, fulfiller_count, created_at}], meta: {...} }
```

### POST /api/wants — 发布求购

```
Body: title*, author, publisher, max_price*, acceptable_condition
逻辑：expires_at = now + 7天，status='active'
```

### GET /api/wants/{id} — 求购详情

```
注意：仅当求购 fulfilled 后，发布者才可见接单人信息
```

### POST /api/wants/{id}/fulfill — 接单

```
Header: Authorization: Bearer <token>
逻辑：创建 want_fulfillment，status='pending'
响应：提示联系平台走正常卖书流程（提交书籍 → 审核 → 上架关联）
```

---

## 五、Admin 模块（Session 认证 + Blade 视图）

### 审核管理

| 方法 | 路径 | 说明 |
|------|------|------|
| GET | /admin/reviews | 待审核书籍列表（status=pending_review） |
| GET | /admin/reviews/{id} | 审核详情（书籍信息+图片） |
| POST | /admin/reviews/{id}/approve | 审核通过 → status='approved'，记录 approved_at |
| POST | /admin/reviews/{id}/reject | 驳回 → status='removed'，填写 reject_reason |
| POST | /admin/reviews/{id}/receive | 确认拿书入库 → status='active'，定售价/收书价，记录 received_at |

### 书籍管理

| 方法 | 路径 | 说明 |
|------|------|------|
| GET | /admin/books | 全部书籍列表（支持按状态筛选） |
| POST | /admin/books/{id}/edit | 编辑书籍信息（调整价格等） |
| POST | /admin/books/{id}/remove | 下架 |

### 订单管理

| 方法 | 路径 | 说明 |
|------|------|------|
| GET | /admin/orders | 订单列表 |
| POST | /admin/orders/{id}/confirm | 确认订单 → status='confirmed' |

### 其他管理

| 方法 | 路径 | 说明 |
|------|------|------|
| GET | /admin/users | 用户管理 |
| GET | /admin/wants | 求购管理 |
| GET/POST | /admin/categories | 分类管理 |
| GET/POST | /admin/colleges | 学院/专业/课程管理 |
