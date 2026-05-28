# 编码规范

## PHP (Laravel)

- PSR-2 / PSR-12
- 类名 PascalCase，方法名 camelCase
- 4 空格缩进，行宽 ≤ 120
- 数组短语法 `[]`
- 写操作用 Eloquent（`find() + save()`），不用 Query Builder 的 `update()`
- Query Builder 仅用于读操作

## JavaScript / Vue

- ES6+，const / let，不用 var
- 变量 camelCase，常量 UPPER_SNAKE_CASE
- 2 空格缩进
- 避免 `v-html`
- **禁止无作用域的全局 DOM 选择器**：`document.querySelector('form')`、`document.querySelector('button')` 等可能命中公共布局元素。关键元素必须加唯一 `id`，JS 用 `#id` 选择器限定作用域，事件回调中优先用 `e.target`。排查时先在 console 跑 `document.querySelectorAll('form')` 确认数量

## 数据库

- 表名：复数小写下划线
- 主键：统一 `id` 自增
- 必备字段：id / created_at / updated_at
- 引擎：InnoDB，编码：utf8mb4，表注释必填
- **软删除**：本项目所有删除操作均为软删除（`SoftDeletes` trait），禁止物理删除数据。调用 `$model->delete()` 自动写入 `deleted_at`，Laravel 查询自动排除已删除记录。需要包含已删除数据时使用 `withTrashed()`，仅查已删除数据用 `onlyTrashed()`。已启用软删除的模型：Book、Category、College、Major、Course（共 5 个）

## 安全

- 密码：bcrypt（`Hash::make()`）
- 输入：后端白名单验证（`$request->validate()`）
- SQL：Eloquent ORM / 参数绑定，禁止拼接用户输入
- XSS：Blade `{{ }}` 转义，存储前 `strip_tags()` 白名单
- CSRF：POST/PUT/DELETE 必须带 Token
- 文件：白名单校验 MIME/扩展名，随机文件名
- API：Token 认证 + throttle 频率限制
- `.env` 不提交，生产 `APP_DEBUG=false`

## API 统一格式

```json
// 成功
{"code": 200, "message": "success", "data": {}}

// 分页
{"code": 200, "message": "success", "data": {"list": [], "meta": {...}}}

// 错误
{"code": 422, "message": "验证失败", "errors": {}}
```

状态码：200 成功 / 400 参数错误 / 401 未认证 / 403 无权限 / 404 不存在 / 422 验证失败 / 500 服务器错误
