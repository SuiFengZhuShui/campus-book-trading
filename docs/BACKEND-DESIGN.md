# 后端架构设计

## 一、目录结构

```
backend/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CancelTimeoutOrders.php    # 超时取消计划任务
│   ├── Events/
│   │   ├── OrderPaid.php
│   │   ├── OrderConfirmed.php
│   │   ├── OrderPickedUp.php
│   │   ├── OrderCompleted.php
│   │   ├── OrderCancelled.php
│   │   ├── BookSubmitted.php
│   │   ├── BookApproved.php
│   │   └── BookRejected.php
│   ├── Exceptions/
│   │   └── BusinessException.php          # 业务异常（统一 code + message）
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                       # API 控制器（Token 认证）
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── BookController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── WantController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── CollegeController.php
│   │   │   │   └── UploadController.php
│   │   │   ├── Admin/                     # 后台控制器（Session 认证 + Blade 视图）
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   ├── BookController.php
│   │   │   │   ├── OrderController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── WantController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   └── CollegeController.php
│   │   │   └── Controller.php             # 基类
│   │   ├── Kernel.php
│   │   ├── Middleware/
│   │   │   ├── ApiAuth.php                # API Token 认证
│   │   │   └── AdminAuth.php              # Admin Session 认证
│   │   ├── Requests/
│   │   │   ├── Api/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   ├── RegisterRequest.php
│   │   │   │   ├── SubmitBookRequest.php
│   │   │   │   ├── CreateOrderRequest.php
│   │   │   │   └── PostWantRequest.php
│   │   │   └── Admin/
│   │   │       ├── ApproveBookRequest.php
│   │   │       └── RejectBookRequest.php
│   │   └── Resources/
│   │       ├── BookResource.php           # API 响应格式转换
│   │       ├── OrderResource.php
│   │       └── WantResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── College.php
│   │   ├── Major.php
│   │   ├── Course.php
│   │   ├── Book.php
│   │   ├── BookImage.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── OrderTimeline.php
│   │   ├── Review.php
│   │   ├── Want.php
│   │   └── WantFulfillment.php
│   ├── Services/
│   │   ├── BookService.php               # 书籍相关业务逻辑
│   │   ├── OrderService.php              # 订单/支付/超时
│   │   ├── WantService.php               # 求购
│   │   └── ReviewService.php             # 评价 + 结算
│   └── Traits/
│       └── ApiResponse.php               # 统一 JSON 响应
├── config/
├── database/
│   ├── migrations/                        # 已创建 13 表
│   └── seeds/                             # 已创建 Users + Categories
├── routes/
│   ├── api.php                            # API 路由
│   └── web.php                            # Web + Admin 路由
└── resources/
    └── views/
        └── admin/                         # Admin Blade 视图
```

---

## 二、分层职责

```
┌─ Request ─→ Controller ─→ Service ─→ Model ─→ DB
│                         ↗
│   FormRequest 校验      业务逻辑     Eloquent ORM
│                         ↓
│                       Events       (写: find()+save()
│                         ↓           读: Query Builder)
│                      Listeners
│                      (通知、日志)
```

| 层 | 职责 | 不能做 |
|------|------|--------|
| Controller | 取参数、调 Service、返回响应 | 不含业务逻辑 |
| Service | 业务逻辑、事务、事件派发 | 不直接操作 Request/Response |
| Model | 关联定义、作用域、访问器 | 不含复杂业务逻辑 |
| FormRequest | 输入校验、权限判断 | 不含业务逻辑 |
| Resource | 格式化输出、字段过滤 | 不含业务逻辑 |

**Service 中写操作用 Eloquent**：`$model->fill($data)->save()`，不用 `Model::where()->update()`。

---

## 三、认证与中间件

### 3.1 双通道认证

| 通道 | 路由前缀 | 认证方式 | 中间件 | 用户角色 |
|------|---------|---------|--------|---------|
| API | `/api/*` | Token（`Authorization: Bearer <token>`） | `api.auth` | student |
| Admin | `/admin/*` | Session（Laravel 内置） | `web` + `admin.auth` | admin |

### 3.2 Token 认证（API）

```php
// 用户登录后生成 token
$token = Str::random(60);
// 存入 users 表 personal_access_token 字段（或独立 tokens 表）

// 中间件验证
$user = User::where('api_token', $token)->first();
if (!$user || !$user->status) { return 401; }
```

> Laravel 5.8 可用自建 token 或 Laravel Passport。初期用 `api_token` 字段简单方案。

### 3.3 Admin 认证

使用 Laravel 内置 Session 认证。管理员通过 `admin.auth` 中间件验证角色。

```php
// AdminAuth middleware
if (Auth::guest() || Auth::user()->role !== 'admin') {
    return redirect()->route('admin.login');
}
```

---

## 四、模型关联

```
User
├── hasMany Book (seller_id)           # 卖出的书
├── hasMany Order (buyer_id)          # 下的订单
├── hasMany Review (user_id)          # 给出的评价
├── hasMany Want (user_id)            # 发布的求购
└── hasMany WantFulfillment (fulfiller_id)  # 接的单

Book
├── belongsTo User (seller_id)
├── belongsTo Category
├── belongsTo Course (nullable)
├── hasMany BookImage
├── hasMany OrderItem
├── hasMany Review
└── hasMany WantFulfillment (book_id)

Order
├── belongsTo User (buyer_id)
├── hasMany OrderItem
├── hasMany OrderTimeline
└── hasOne Review

Category
└── hasMany Book

Course
├── belongsTo Major
└── hasMany Book

Major
├── belongsTo College
└── hasMany Course

College
└── hasMany Major

Want
├── belongsTo User
└── hasMany WantFulfillment

WantFulfillment
├── belongsTo Want
├── belongsTo User (fulfiller_id)
└── belongsTo Book (nullable)
```

---

## 五、事件与监听

### 5.1 事件列表

| 事件 | 触发时机 | 监听器行为 |
|------|---------|-----------|
| `BookSubmitted` | 学生提交卖书 | 无（后续可通知管理员） |
| `BookApproved` | 管理员审核通过 | 记录时间线 |
| `BookRejected` | 管理员驳回 | 记录驳回原因 |
| `OrderPaid` | 买家支付成功 | 写 order_timeline |
| `OrderConfirmed` | 管理员确认 | 写 order_timeline |
| `OrderPickedUp` | 买家确认取书 | 写 order_timeline |
| `OrderCompleted` | 买家评价完成 | 结算：seller_paid=1, book→sold |
| `OrderCancelled` | 订单取消 | 恢复书籍 active，写 timeline |
| `WantExpired` | 求购过期 | 无（status 直接由计划任务改） |

### 5.2 事件服务提供者

```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    OrderPaid::class => [
        CreateOrderTimeline::class,     // 写入订单时间线
    ],
    OrderCompleted::class => [
        SettleSeller::class,            // 卖家结算 + 书 status→sold
    ],
    OrderCancelled::class => [
        RestoreBooks::class,            // 书籍恢复 active
        CreateOrderTimeline::class,
    ],
];
```

---

## 六、Service 设计

### 6.1 OrderService

```php
class OrderService
{
    // 创建订单（事务）
    public function create(int $userId, array $bookIds, string $pickupLocation): Order

    // 模拟支付
    public function pay(int $orderId): void

    // 管理员确认
    public function confirm(int $orderId): void

    // 取消订单（买家或超时）
    public function cancel(int $orderId, ?string $reason): void

    // 买家确认取书
    public function pickup(int $orderId): void

    // 超时扫描（计划任务调用）
    public function cancelTimeoutOrders(): void

    // 生成订单号
    private function generateOrderNo(): string
}
```

**关键方法实现要点**

`create()`：
```
事务 {
  SELECT book_ids FOR UPDATE（防并发）
  检查每本书 status='active' → 否则抛 BusinessException
  INSERT orders + order_items（价格快照）
  派发 OrderCreated 事件
}
```

`cancelTimeoutOrders()`：
```
pending 超 30 分钟 → cancel()
paid 超 48 小时 → cancel()
wants.active 超 expires_at → status='expired'
```

### 6.2 BookService

```php
class BookService
{
    // 学生提交卖书
    public function submit(array $data, array $images): Book

    // 管理员审核通过（定售价+收书价）
    public function approve(int $bookId, array $data): void

    // 驳回
    public function reject(int $bookId, string $reason): void

    // 确认入库
    public function receive(int $bookId, ?float $price, ?float $costPrice): void

    // 下架
    public function remove(int $bookId): void

    // 编辑
    public function update(int $bookId, array $data): void

    // 售价建议算法
    public function suggestPrice(string $title, string $author, string $publisher, float $originalPrice): float
}
```

**`suggestPrice()` 算法**：
```
SELECT AVG(price) FROM books
WHERE title = $title AND author = $author AND publisher = $publisher AND status = 'active'

有同类书 → 返回平均价格
无同类书 → 返回 $originalPrice * 0.5
```

### 6.3 ReviewService

```php
class ReviewService
{
    // 买家评价（事务）
    public function review(int $orderId, int $bookRating, int $serviceRating, ?string $comment): void
    // → 创建 review → order.status='completed' → book.seller_paid=1 → book.status='sold'
}
```

### 6.4 WantService

```php
class WantService
{
    // 发布求购
    public function create(array $data): Want

    // 接单
    public function fulfill(int $wantId, int $userId): void

    // 关联书籍（管理员审核上架后调用）
    public function linkBook(int $fulfillmentId, int $bookId): void
}
```

---

## 七、错误码体系

### 7.1 HTTP 状态码

| code | 含义 | 场景 |
|------|------|------|
| 200 | 成功 | 正常请求 |
| 400 | 参数错误 | 缺少必填参数 |
| 401 | 未认证 | token 无效/过期/未登录 |
| 403 | 无权限 | 非本人操作、角色不符 |
| 404 | 不存在 | 书籍/订单/求购不存在 |
| 422 | 验证失败 | 字段校验不通过 |
| 429 | 频率限制 | API 调用过频 |
| 500 | 服务器错误 | 未知异常 |

### 7.2 业务错误码（message 字段）

| 场景 | message |
|------|---------|
| 手机号已注册 | "该手机号已注册" |
| 学号已注册 | "该学号已被使用" |
| 手机号或密码错误 | "手机号或密码错误" |
| 账号已禁用 | "账号已被禁用，请联系管理员" |
| 书籍已被购买 | "《xxx》已被他人购买，请重新下单" |
| 订单状态不允许此操作 | "当前订单状态不允许此操作" |
| 订单不属于当前用户 | "无权查看此订单" |
| 不是购买者 | "仅购买者可查看卖家信息" |
| 图片不足 3 张 | "请至少上传 3 张图片" |
| 不能接自己的求购 | "不能接自己的求购" |
| 求购已过期 | "该求购已过期" |
| 重复评价 | "该订单已评价" |
| 分类有关联书籍 | "该分类下有 N 本书，无法删除" |
| 学院/专业有子数据 | "该学院下有 N 个专业，无法删除" |

### 7.3 统一响应封装（ApiResponse Trait）

```php
trait ApiResponse
{
    protected function success($data = null, string $message = 'success')
    {
        return response()->json(['code' => 200, 'message' => $message, 'data' => $data]);
    }

    protected function paginate($list, $meta)
    {
        return response()->json(['code' => 200, 'message' => 'success', 'data' => ['list' => $list, 'meta' => $meta]]);
    }

    protected function error(int $code, string $message, $errors = null)
    {
        $body = ['code' => $code, 'message' => $message];
        if ($errors) $body['errors'] = $errors;
        return response()->json($body, $code);
    }
}
```

---

## 八、通知机制

### 8.1 通知场景

| 场景 | 通知对象 | 内容 |
|------|---------|------|
| 审核通过 | 卖书学生 | "你的《xxx》已通过审核，等待平台取书" |
| 审核驳回 | 卖书学生 | "你的《xxx》未通过审核，原因：xxx" |
| 书籍上架 | 卖书学生 | "你的《xxx》已上架" |
| 书籍售出 | 卖书学生 | "你的《xxx》已被购买" |
| 订单支付 | 买家 | "订单 #xxx 支付成功" |
| 订单确认 | 买家 | "订单 #xxx 已确认，请取书" |
| 订单取消 | 买家 | "订单 #xxx 已取消" |
| 求购接单 | 发布者 | "有人接了你的求购《xxx》" |
| 求购满足 | 发布者 | "你的求购《xxx》已有书可购买" |

### 8.2 实现方案

**初期（最简）**：订单状态变更后，前端轮询或刷新即可看到。不依赖推送。

**后续可选接入**：
- 微信小程序订阅消息（`uni.requestSubscribeMessage`）
- 站内消息表（`notifications` 表，前端轮询）

> 当前项目阶段不建 notifications 表，不接入推送。订单状态通过 API 返回当前状态，前端根据状态显示对应操作按钮。

---

## 九、测试策略

### 9.1 测试分层

| 层 | 框架 | 目标覆盖率 | 说明 |
|-----|------|-----------|------|
| 单元测试 | PHPUnit | 80% | Service 层业务逻辑 |
| 接口测试 | PHPUnit + `$this->json()` | 80% | 每个 API 端点 |
| Admin 功能测试 | PHPUnit + `$this->actingAs()` | 60% | 后台核心流程 |

### 9.2 核心测试用例

**认证模块**
```
✅ 注册 — 正常注册、手机号重复、学号重复、密码过短
✅ 登录 — 正常登录、密码错误、账号禁用
✅ Token — 无 token 访问需认证接口返回 401
```

**书籍模块**
```
✅ 提交卖书 — 正常提交、图片不足 3 张
✅ 审核通过 — status→approved、approved_at 记录
✅ 驳回 — status→removed、reject_reason 必填
✅ 确认入库 — status→active、received_at 记录
✅ 公开列表 — 仅返回 status='active'、不返回 seller 信息
✅ 详情 — 非购买者不可见卖家信息
```

**订单模块**
```
✅ 创建订单 — 正常下单、书籍非 active 时失败
✅ 支付 — status→paid
✅ 取消 — 书籍恢复 active
✅ 超时取消 — pending 30min、paid 48h
✅ 评价 — status→completed、seller_paid=1、books.status→sold
✅ 卖家信息 — 仅购买者可查看（paid/confirmed/picked_up/completed 状态）
```

**求购模块**
```
✅ 发布求购 — expires_at = now+7day
✅ 接单 — 不能接自己的
✅ 过期 — 计划任务扫描
```

**并发测试**
```
✅ 两用户同时下单同一本书 → 仅一人成功（FOR UPDATE 锁）
```

### 9.3 测试辅助

```php
// tests/TestCase.php 中注册
use DatabaseMigrations;        // 每个测试自动 migration
use WithoutMiddleware;         // 跳过 CSRF/Throttle（API 测试）
use RefreshDatabase;           // 或用此 Trait 更快

// 工厂
$factory->define(User::class, ...)
$factory->define(Book::class, ...)
```

---

## 十、部署运维

### 10.1 Nginx 配置要点

```nginx
server {
    listen 80;
    server_name campus-books.local;
    root /path/to/backend/public;

    index index.php;
    charset utf-8;

    # API 重写
    location /api/ {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Admin + Web
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # 静态资源
    location /storage/ {
        alias /path/to/backend/storage/app/public/;
    }

    # 安全
    location ~ /\. {
        deny all;
    }
}
```

### 10.2 HTTPS 安全头

```nginx
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header X-Content-Type-Options nosniff;
add_header X-Frame-Options DENY;
add_header Referrer-Policy strict-origin-when-cross-origin;
```

### 10.3 定时任务

| 任务 | 频率 | 说明 |
|------|------|------|
| `cancel-timeout-orders` | 每分钟 | 超时订单取消 + 求购过期 |
| `clean-temp-images` | 每小时 | 清理 temp 目录超过 24h 的文件 |

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('orders:cancel-timeout')->everyMinute();
    $schedule->command('images:clean-temp')->hourly();
}
```

### 10.4 环境变量清单

```ini
APP_NAME=校园二手书
APP_ENV=production
APP_DEBUG=false
APP_URL=https://campus-books.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campus_books
DB_USERNAME=xxx
DB_PASSWORD=xxx

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 10.5 上线检查清单

- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` 已生成且唯一
- [ ] 数据库密码已修改
- [ ] `storage/` 和 `bootstrap/cache/` 可写
- [ ] `php artisan storage:link` 已执行
- [ ] 定时任务已配置（crontab 每分钟执行 `php artisan schedule:run`）
- [ ] HTTPS 已启用
- [ ] 安全头已配置
- [ ] 目录浏览已关闭
- [ ] 服务器版本号已隐藏

---

## 十一、控制器伪代码示例

### 11.1 Api BookController

```php
class BookController extends Controller
{
    use ApiResponse;

    // POST /api/books/submit
    public function submit(SubmitBookRequest $request)
    {
        $book = app(BookService::class)->submit(
            $request->validated(),
            $request->file('images', [])
        );
        return $this->success(new BookResource($book), '提交成功');
    }

    // GET /api/books
    public function index(Request $request)
    {
        $books = Book::where('status', 'active')
            ->when($request->category_id, fn($q, $v) => $q->where('category_id', $v))
            ->when($request->keyword, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('title', 'like', "%$v%")->orWhere('author', 'like', "%$v%");
            }))
            ->with('images')
            ->orderBy('received_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return $this->paginate(BookResource::collection($books), [
            'current_page' => $books->currentPage(),
            'per_page' => $books->perPage(),
            'total' => $books->total(),
            'last_page' => $books->lastPage(),
        ]);
    }

    // GET /api/books/{id}
    public function show($id)
    {
        $book = Book::with(['images', 'reviews.user'])->findOrFail($id);
        $data = (new BookResource($book))->toArray(request());
        // 卖家信息仅购买者可见
        if ($this->canViewSeller($book)) {
            $data['seller'] = ['name' => $book->seller->name, 'phone' => $this->maskPhone($book->seller->phone)];
        }
        return $this->success($data);
    }

    private function canViewSeller(Book $book): bool
    {
        $user = request()->user();
        if (!$user) return false;
        return OrderItem::where('book_id', $book->id)
            ->whereHas('order', fn($q) => $q->where('buyer_id', $user->id)
                ->whereIn('status', ['paid', 'confirmed', 'picked_up', 'completed']))
            ->exists();
    }

    private function maskPhone(string $phone): string
    {
        return substr($phone, 0, 3) . '****' . substr($phone, 7);
    }
}
```

### 11.2 Admin ReviewController

```php
class ReviewController extends Controller
{
    // GET /admin/reviews
    public function index(Request $request)
    {
        $books = Book::with('seller', 'images')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->keyword, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('title', 'like', "%$v%")->orWhere('author', 'like', "%$v%");
            }))
            ->orderBy('submitted_at', 'desc')
            ->paginate(15);

        return view('admin.reviews.index', compact('books'));
    }

    // GET /admin/reviews/{id}
    public function detail($id)
    {
        $book = Book::with('seller', 'images', 'category', 'course')->findOrFail($id);
        return view('admin.reviews.detail', compact('book'));
    }

    // POST /admin/reviews/{id}/approve
    public function approve(int $id, ApproveBookRequest $request)
    {
        app(BookService::class)->approve($id, $request->validated());
        return redirect()->route('admin.reviews.index')->with('success', '审核通过');
    }

    // POST /admin/reviews/{id}/reject
    public function reject(int $id, RejectBookRequest $request)
    {
        app(BookService::class)->reject($id, $request->input('reason'));
        return redirect()->route('admin.reviews.index')->with('success', '已驳回');
    }

    // POST /admin/reviews/{id}/receive
    public function receive(int $id, Request $request)
    {
        app(BookService::class)->receive($id, $request->input('price'), $request->input('cost_price'));
        return redirect()->route('admin.reviews.index')->with('success', '已入库上架');
    }
}
```

---

## 十二、开发实施清单

按此架构，实施顺序为：

| 阶段 | 内容 | 产出 |
|------|------|------|
| 1 | 基类 + Trait + 中间件 | `Controller.php`, `ApiResponse`, `ApiAuth`, `AdminAuth` |
| 2 | 路由注册 | `routes/api.php` + `routes/web.php` |
| 3 | FormRequest | 6 个 Request 类 |
| 4 | Service 层 | `BookService`, `OrderService`, `WantService`, `ReviewService` |
| 5 | 事件 + 监听器 | 8 个事件 + 对应监听器 |
| 6 | Controller 层 | 7 个 Api 控制器 + 9 个 Admin 控制器 |
| 7 | 计划任务 | `CancelTimeoutOrders` + Kernel 注册 |
| 8 | Admin Blade 视图 | 按 FRONTEND-DESIGN 逐页实现 |
| 9 | 测试 | 单元测试 + 接口测试 |

> 每个阶段完成后运行测试，确认无误再进入下一阶段。
