# 部署检查清单 — 校园二手书平台

## 服务器环境要求

- [ ] PHP >= 7.1.3（推荐 7.4 或 8.0）
- [ ] PHP 扩展：pdo_mysql, mbstring, openssl, gd, fileinfo, tokenizer, xml, ctype, json, bcmath
- [ ] MySQL 5.7+（数据库名 campus_books，字符集 utf8mb4）
- [ ] Nginx 1.18+
- [ ] Composer 2.x
- [ ] Node.js + npm（用于前端构建）
- [ ] Git

## 部署步骤

### 1. 域名和 DNS
- [ ] `xiaozhanan.xyz` A 记录指向 `8.134.179.51`
- [ ] `www.xiaozhanan.xyz` CNAME 指向 `xiaozhanan.xyz`

### 2. 拉取代码
```bash
cd /var/www
git clone <仓库地址> backend
cd backend
```

### 3. 安装依赖
```bash
# PHP 依赖（生产模式，跳过开发依赖）
composer install --no-dev --optimize-autoloader

# 前端依赖 + 生产构建
npm ci
npm run production
```

### 4. 环境配置
```bash
cp .env.example .env
# 编辑 .env，填写实际值：
#   - DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD
#   - MAIL_* 邮件配置
#   其他已预填，无需更改
php artisan key:generate
```

### 5. 数据库
```bash
# 创建数据库（MySQL）
# CREATE DATABASE campus_books CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 运行迁移
php artisan migrate --force

# （可选）种子数据
php artisan db:seed --force
```

### 6. 存储链接
```bash
rm -rf public/storage
php artisan storage:link
```

### 7. Laravel 缓存优化
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. 文件权限
```bash
chown -R www-data:www-data /var/www/backend
find /var/www/backend -type d -exec chmod 755 {} \;
find /var/www/backend -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
chmod 600 .env
```

### 9. Nginx 配置
```bash
# 将 deploy/nginx.conf 复制到服务器配置目录
cp deploy/nginx.conf /etc/nginx/sites-available/xiaozhanan.xyz

# 启用站点
ln -s /etc/nginx/sites-available/xiaozhanan.xyz /etc/nginx/sites-enabled/

# 测试配置
nginx -t

# 重载 Nginx
systemctl reload nginx
```

### 10. SSL 证书（Let's Encrypt）
```bash
# 安装 certbot
apt install certbot python3-certbot-nginx

# 申请证书
certbot --nginx -d xiaozhanan.xyz -d www.xiaozhanan.xyz

# 设置自动续期（已自动配置，验证）
certbot renew --dry-run
```

### 11. 重启服务
```bash
systemctl restart php-fpm  # 按实际版本调整，如 php8.0-fpm
systemctl reload nginx
```

## 验证清单

- [ ] `https://xiaozhanan.xyz` 正常加载首页
- [ ] HTTP 自动跳转到 HTTPS
- [ ] 浏览器显示安全锁图标
- [ ] 注册/登录功能正常
- [ ] 卖书/买书功能正常
- [ ] 购物车/下单功能正常
- [ ] 管理后台 `/admin` 正常
- [ ] 图片上传和显示正常
- [ ] 检查 `storage/logs/` 无异常错误
- [ ] Cookie 安全属性正确（Secure + Domain）
- [ ] 静态资源带缓存头（Cache-Control）

## 回滚

如有问题：
```bash
# 清除缓存
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 从备份恢复数据库
# 修改 .env APP_DEBUG=true 查看详细错误
```

## 定时任务（可选）

如需自动取消超时订单：
```bash
# 添加 crontab
crontab -e
# 每分钟检查一次
* * * * * cd /var/www/backend && php artisan schedule:run >> /dev/null 2>&1
```
