<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '校园二手书网')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=LXGW+WenKai+TC:wght@300;400;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --cream: #ebe3d4;
            --parchment: #e5dcc8;
            --ink: #2c2416;
            --gold: #b49450;
            --gold-light: #d4bc7c;
            --bronze: #8b6914;
            --wine: #6b2737;
            --moss: #4a6741;
            --muted: #6e6559;
            --border: #cec4b0;
            --card: #fffdf9;
        }
        body {
            font-family: 'LXGW WenKai TC', 'Cormorant Garamond', serif;
            background: var(--cream);
            color: var(--ink);
            line-height: 1.8;
            min-height: 100vh;
        }
        body::before {
            content: ''; position: fixed; inset: 0; z-index: -1; pointer-events: none;
            background:
                radial-gradient(ellipse at 20% 20%, rgba(180,148,80,0.04) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 60%, rgba(107,39,55,0.03) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 80%, rgba(74,103,65,0.03) 0%, transparent 50%);
        }
        a { text-decoration: none; color: inherit; }

        /* 导航 — 暖羊皮纸毛玻璃，与奶油底色形成层次 */
        .header {
            background: rgba(247,241,230,0.94);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 12px rgba(44,36,22,0.04);
        }
        .header-inner {
            max-width: 1200px; margin: 0 auto; padding: 0 32px;
            display: flex; align-items: center; height: 68px; gap: 24px;
        }
        .logo {
            display: flex; align-items: baseline; gap: 4px;
            font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 600;
            color: var(--ink); text-decoration: none; letter-spacing: -0.01em;
            white-space: nowrap;
        }
        .logo .amp { font-style: italic; color: var(--gold); font-size: 28px; font-weight: 700; }
        .logo .sub {
            font-family: 'LXGW WenKai TC', serif; font-size: 11px; color: var(--muted);
            letter-spacing: 0.06em; margin-left: 2px;
        }

        .nav-links { display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .nav-links a {
            padding: 8px 16px; border-radius: 20px;
            color: var(--muted); text-decoration: none;
            transition: all 0.3s;
        }
        .nav-links a:hover, .nav-links a.active {
            color: var(--ink); background: rgba(180,148,80,0.08);
        }

        .college-select {
            height: 38px; padding: 0 32px 0 14px;
            border: 1px solid var(--border); border-radius: 20px;
            font-family: 'LXGW WenKai TC', serif; font-size: 13px;
            color: var(--ink); background: var(--card);
            cursor: pointer; -webkit-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23b49450'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center;
            outline: none; min-width: 164px;
            transition: border-color 0.3s;
        }
        .college-select:hover { border-color: var(--gold); }
        .college-select:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(180,148,80,0.12); }
        .college-select option { color: var(--ink); background: var(--card); }

        /* 搜索框 */
        .search-box {
            flex: 1; display: flex; max-width: 420px;
        }
        .search-input-wrap {
            flex: 1; position: relative;
        }
        .search-input-wrap input {
            width: 100%; height: 38px;
            border: 1px solid var(--border); border-right: none;
            border-radius: 20px 0 0 20px;
            padding: 0 34px 0 16px;
            font-family: 'LXGW WenKai TC', serif; font-size: 13px;
            outline: none; background: var(--card); color: var(--ink);
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .search-input-wrap input:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(180,148,80,0.12);
        }
        .search-box .clear-btn {
            position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            width: 20px; height: 20px; border: none;
            background: var(--gold-light); color: #fff; border-radius: 50%;
            cursor: pointer; font-size: 12px; line-height: 20px;
            text-align: center; padding: 0; display: none;
            transition: background 0.2s;
        }
        .search-box .clear-btn.visible { display: block; }
        .search-box .clear-btn:hover { background: var(--gold); }
        .search-box button {
            height: 38px; padding: 0 20px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #fff; border: none; border-radius: 0 20px 20px 0;
            font-family: 'LXGW WenKai TC', serif; font-size: 13px; font-weight: 700;
            cursor: pointer; white-space: nowrap;
            transition: opacity 0.2s, box-shadow 0.2s;
        }
        .search-box button:hover { opacity: 0.9; box-shadow: 0 2px 12px rgba(180,148,80,0.25); }

        /* 右侧操作 */
        .header-actions {
            display: flex; align-items: center; gap: 10px;
            white-space: nowrap; margin-left: auto;
        }
        .btn-sell {
            padding: 8px 20px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #fff; border-radius: 20px;
            font-size: 13px; font-weight: 700;
            box-shadow: 0 2px 8px rgba(180,148,80,0.2);
            transition: all 0.3s;
        }
        .btn-sell:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(180,148,80,0.3); }
        .btn-login {
            padding: 8px 18px; border: 1px solid var(--border); border-radius: 20px;
            color: var(--muted); font-size: 13px;
            transition: all 0.3s;
        }
        .btn-login:hover { border-color: var(--gold); color: var(--gold); }
        .user-name {
            font-size: 13px; color: var(--muted);
            max-width: 80px; overflow: hidden; text-overflow: ellipsis;
        }
        .header-actions > a[href="/orders"],
        .header-actions > a[href="/my-sells"] {
            font-size: 13px; color: var(--muted); padding: 6px 10px; border-radius: 20px;
            transition: all 0.3s;
        }
        .header-actions > a[href="/orders"]:hover,
        .header-actions > a[href="/my-sells"]:hover {
            background: rgba(180,148,80,0.08); color: var(--gold);
        }

        /* 主体 */
        .main {
            max-width: 1200px; margin: 0 auto; padding: 32px 24px;
        }

        /* 成功提示 */
        .flash-success {
            background: rgba(74,103,65,0.08);
            color: var(--moss); padding: 12px 16px;
            border-radius: 12px; margin-bottom: 20px;
            font-size: 14px; border: 1px solid rgba(74,103,65,0.15);
        }

        /* 书籍网格 */
        .book-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        @media (max-width: 1024px) { .book-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) {
            .book-grid { grid-template-columns: repeat(2, 1fr); }
            .college-select { min-width: 120px; }
            .search-box { max-width: 200px; }
        }
        @media (max-width: 480px) { .book-grid { grid-template-columns: 1fr; } }

        .book-card {
            background: var(--card); border-radius: 16px; overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            transition: all 0.4s cubic-bezier(0.22,0.61,0.36,1);
            cursor: pointer; display: block;
        }
        .book-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.08);
            border-color: var(--gold-light);
        }
        .book-cover {
            width: 100%; height: 220px; object-fit: cover;
            background: linear-gradient(135deg, #f5efe0, #ebe0cd);
            display: flex; align-items: center; justify-content: center;
            color: #d4ccb8; font-size: 48px;
        }
        .book-info { padding: 18px 20px; }
        .book-title {
            font-size: 16px; font-weight: 700; color: var(--ink);
            line-height: 1.5; display: -webkit-box;
            -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden; min-height: 48px;
        }
        .book-meta {
            margin-top: 10px; font-size: 12px; color: var(--muted);
            display: flex; justify-content: space-between; align-items: center;
        }
        .book-price {
            margin-top: 10px; display: flex; align-items: baseline; gap: 8px;
        }
        .book-price-current {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px; color: var(--wine); font-weight: 700;
        }
        .book-price-current::before { content: '\00a5'; font-size: 13px; }
        .book-price-original {
            font-size: 13px; color: #d4ccb8; text-decoration: line-through;
        }
        .condition-tag {
            display: inline-block; padding: 3px 8px; border-radius: 12px;
            font-size: 11px; font-weight: 500;
            background: rgba(74,103,65,0.1); color: var(--moss);
        }

        /* 分页 */
        .pagination {
            display: flex; justify-content: center; gap: 4px;
            margin-top: 40px; list-style: none; padding: 0;
        }
        .pagination li { margin: 0; }
        .pagination .page-link {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 38px; height: 38px; padding: 0 10px;
            border-radius: 20px; font-size: 14px;
            background: var(--card); border: 1px solid var(--border);
            color: var(--muted); transition: all 0.3s ease;
        }
        .pagination .page-link:hover {
            background: rgba(180,148,80,0.06); color: var(--gold);
            border-color: var(--gold);
        }
        .pagination .active .page-link {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #fff; border-color: transparent;
            font-weight: 600; box-shadow: 0 2px 8px rgba(180,148,80,0.25);
        }
        .pagination .disabled .page-link {
            color: #d4ccb8; cursor: not-allowed; pointer-events: none;
        }

        .no-books {
            text-align: center; padding: 80px 20px; color: var(--muted);
        }
        .no-books .icon { font-size: 64px; margin-bottom: 16px; }

        /* ====== 表单通用样式 ====== */
        .form-card {
            background: var(--card); border-radius: 16px;
            padding: 40px 36px; border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(0,0,0,0.04);
        }
        .form-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 24px; font-weight: 600; color: var(--ink);
            letter-spacing: -0.01em; margin-bottom: 28px; text-align: center;
        }
        .form-title::after {
            content: '✦';
            display: block; font-size: 12px; color: var(--gold);
            margin: 12px auto 0;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 500;
            color: var(--muted); margin-bottom: 6px;
        }
        .form-group label .required { color: var(--wine); }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="password"],
        .form-group select,
        .form-group textarea {
            width: 100%; height: 44px; padding: 0 14px;
            border: 1px solid var(--border); border-radius: 12px;
            font-family: 'LXGW WenKai TC', serif; font-size: 14px;
            color: var(--ink); background: #fefdfb; outline: none;
            transition: border-color 0.3s cubic-bezier(0.25,0.1,0.25,1),
                        box-shadow 0.3s cubic-bezier(0.25,0.1,0.25,1);
        }
        .form-group textarea { height: 100px; padding: 12px 14px; resize: vertical; }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(180,148,80,0.12);
        }
        .form-group .hint { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .btn-primary {
            width: 100%; height: 46px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #fff; border: none; border-radius: 24px;
            font-family: 'LXGW WenKai TC', serif;
            font-size: 16px; font-weight: 700; letter-spacing: 0.02em;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(180,148,80,0.2);
            transition: transform 0.15s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(180,148,80,0.35); }
        .btn-primary:active { transform: translateY(0); box-shadow: 0 1px 4px rgba(180,148,80,0.15); }
        .alert-error {
            background: rgba(107,39,55,0.06); color: var(--wine);
            padding: 12px 16px; border-radius: 12px;
            margin-bottom: 20px; font-size: 13px;
            border: 1px solid rgba(107,39,55,0.15);
        }

        /* 底部链接 */
        .form-footer-links {
            text-align: center; margin-top: 20px;
            display: flex; justify-content: center; gap: 20px;
        }
        .form-footer-links a {
            font-size: 13px; color: var(--muted); transition: color 0.3s;
        }
        .form-footer-links a:hover { color: var(--gold); }

        /* ====== 文件上传 ====== */
        .file-upload-area {
            border: 2px dotted var(--border); border-radius: 14px;
            padding: 36px; text-align: center; cursor: pointer;
            background: #fefdfb; transition: border-color 0.3s, background 0.3s;
        }
        .file-upload-area:hover { border-color: var(--gold); background: rgba(180,148,80,0.03); }
        .file-upload-area .icon { font-size: 40px; margin-bottom: 8px; }
        .file-upload-area p { font-size: 13px; color: var(--muted); margin: 0; }
        .preview-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 10px; margin-top: 12px;
        }
        .preview-item {
            position: relative; aspect-ratio: 3/4;
            border-radius: 12px; overflow: hidden;
            background: var(--parchment);
        }
        .preview-item img { width: 100%; height: 100%; object-fit: cover; }
        .preview-item .remove-btn {
            position: absolute; top: 4px; right: 4px;
            width: 22px; height: 22px; border-radius: 50%;
            background: rgba(107,39,55,0.8); color: #fff;
            border: none; cursor: pointer; font-size: 14px;
            line-height: 22px; text-align: center; padding: 0;
            transition: background 0.15s;
        }
        .preview-item .remove-btn:hover { background: var(--wine); }

        /* ====== 工具类 ====== */
        .divider-amber {
            width: 48px; height: 2px;
            background: linear-gradient(90deg, var(--gold), transparent);
            border-radius: 1px; margin: 16px 0;
        }
        .surface-warm {
            background: rgba(180,148,80,0.04); border-radius: 14px;
            padding: 24px; border: 1px solid rgba(180,148,80,0.08);
        }
        .accent-dot {
            display: inline-block; width: 6px; height: 6px;
            border-radius: 50%; background: var(--gold);
            margin-right: 8px; vertical-align: middle;
        }
        .btn-ghost {
            display: inline-flex; align-items: center; justify-content: center;
            background: transparent; border: 1px solid var(--border);
            color: var(--ink); border-radius: 20px;
            padding: 8px 18px; font-size: 13px;
            cursor: pointer; transition: all 0.3s ease;
        }
        .btn-ghost:hover { background: rgba(180,148,80,0.06); border-color: var(--gold); color: var(--gold); }

        /* ====== 金色按钮（主要CTA） ====== */
        .btn-amber {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 12px 28px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #fff; border: none; border-radius: 24px;
            font-family: 'LXGW WenKai TC', serif;
            font-size: 16px; font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(180,148,80,0.3);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-amber:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(180,148,80,0.4); }
        .btn-amber:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(180,148,80,0.2); }

        /* ====== 酒红按钮（次要CTA，如购买） ====== */
        .btn-wine {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--wine), #8b3a4a);
            color: #fff; border: none; border-radius: 20px;
            font-size: 13px; font-weight: 600;
            cursor: pointer; box-shadow: 0 2px 8px rgba(107,39,55,0.2);
            transition: all 0.3s;
        }
        .btn-wine:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(107,39,55,0.3); }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="logo">
                Campus<span class="amp">&amp;</span>Books<span class="sub">· 校园二手书</span>
            </a>

            <nav class="nav-links">
                <a href="/" class="{{ request()->is('/') && !request('category_id') ? 'active' : '' }}">首页</a>
                <a href="/wants" class="{{ request()->is('wants*') ? 'active' : '' }}">求购</a>
                <select class="college-select" onchange="location.href=this.value ? '/?category_id='+this.value : '/'">
                    <option value="">✦ 全部学院</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </nav>

            <form class="search-box" action="/search" method="GET">
                <div class="search-input-wrap">
                    <input type="text" name="keyword" id="search-input" placeholder="搜索书名、作者…"
                           value="{{ request('keyword') }}" @if(request('keyword')) autofocus onfocus="this.setSelectionRange(this.value.length,this.value.length)" @endif>
                    <span class="clear-btn {{ request('keyword') ? 'visible' : '' }}" id="clear-search"
                          onclick="document.getElementById('search-input').value='';this.classList.remove('visible');location.href='/'">&times;</span>
                </div>
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                <button type="submit">搜索</button>
            </form>
            <script>
                document.getElementById('search-input').addEventListener('input', function() {
                    document.getElementById('clear-search').classList.toggle('visible', this.value.length > 0);
                });
            </script>

            <div class="header-actions">
                <a href="/sell" class="btn-sell">✦ 卖书</a>
                @guest
                    <a href="/login" class="btn-login">登录</a>
                @else
                    <a href="/orders" style="font-size: 13px; color: var(--muted); padding: 6px 10px; border-radius: 20px; transition: all 0.3s;">我的订单</a>
                    <a href="/my-sells" style="font-size: 13px; color: var(--muted); padding: 6px 10px; border-radius: 20px; transition: all 0.3s;">我的卖书</a>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <a href="javascript:document.getElementById('logout-form').submit();" class="btn-login">退出</a>
                    <form id="logout-form" action="/logout" method="POST" style="display:none;">@csrf</form>
                @endguest
            </div>
        </div>
    </header>

    <main class="main">
        @if(session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>

    <footer style="text-align: center; padding: 40px 20px; color: var(--muted); font-size: 12px; border-top: 1px solid var(--border);">
        <span>✦ 校园二手书网 © 2026 ✦</span>
    </footer>
</body>
</html>
