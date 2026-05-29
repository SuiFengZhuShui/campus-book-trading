<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '校园二手书') — 管理后台</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,500&family=LXGW+WenKai+TC:wght@300;400;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --cream: #fdfaf4;
            --ink: #2c2416;
            --gold: #b49450;
            --gold-light: #d4bc7c;
            --wine: #6b2737;
            --moss: #4a6741;
            --muted: #8c8478;
            --border: #e5dccf;
            --card: #fffdf9;
            --sidebar-bg: #1a1510;
        }
        body {
            font-family: 'LXGW WenKai TC', -apple-system, 'PingFang SC', 'Microsoft YaHei', sans-serif;
            background: var(--cream);
            color: var(--ink);
        }

        .layout { display: flex; height: 100vh; }

        /* 侧边栏 — 暖棕深色 */
        .sidebar {
            width: 240px;
            background: linear-gradient(180deg, #1a1510 0%, #2c2416 40%, #3d3020 100%);
            color: #fff;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .sidebar .logo {
            padding: 26px 22px;
            font-family: 'Cormorant Garamond', serif;
            font-size: 20px; font-weight: 600;
            border-bottom: 1px solid rgba(180,148,80,0.15);
            display: flex; align-items: center; gap: 8px;
            letter-spacing: -0.01em;
        }
        .sidebar .logo .icon { font-size: 22px; }
        .sidebar .logo::after {
            content: '✦'; font-size: 10px; color: var(--gold);
            margin-left: auto;
        }
        .sidebar .nav {
            flex: 1; padding: 12px 0;
        }
        .sidebar .nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 22px;
            color: rgba(255,255,255,0.55);
            text-decoration: none; font-size: 14px;
            transition: all 0.2s cubic-bezier(0.25,0.1,0.25,1);
            border-left: 3px solid transparent;
        }
        .sidebar .nav a:hover {
            color: #fff; background: rgba(255,255,255,0.04);
            border-left-color: rgba(180,148,80,0.5);
        }
        .sidebar .nav a.active {
            color: #fff; background: rgba(180,148,80,0.1);
            border-left-color: var(--gold); font-weight: 500;
        }

        /* 主区域 */
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; overflow-y: auto; }

        /* 顶栏 */
        .topbar {
            background: var(--card);
            padding: 0 32px; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(44,36,22,0.03);
        }
        .topbar .site-name { font-size: 13px; color: var(--muted); }
        .topbar .site-name::before {
            content: '✦'; font-size: 8px; color: var(--gold);
            margin-right: 8px; vertical-align: middle;
        }
        .topbar .user-info {
            display: flex; align-items: center; gap: 10px;
            font-size: 14px; color: var(--muted);
        }
        .topbar .user-info .name { font-weight: 500; color: var(--ink); }
        .topbar .user-info a {
            color: var(--wine); text-decoration: none;
            font-size: 13px; padding: 4px 12px; border-radius: 20px;
            transition: background 0.15s;
        }
        .topbar .user-info a:hover { background: rgba(107,39,55,0.06); }

        /* 内容区 */
        .content { padding: 32px; flex: 1; }

        .breadcrumb {
            font-size: 13px; color: var(--muted); margin-bottom: 12px;
        }
        .breadcrumb a { color: var(--muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--gold); }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 26px; font-weight: 600; color: var(--ink);
            letter-spacing: -0.01em; margin-bottom: 24px;
        }
        .page-title::after {
            content: ''; display: block; width: 48px; height: 2px;
            background: linear-gradient(90deg, var(--gold), transparent);
            margin-top: 8px;
        }

        /* 卡片 */
        .card {
            background: var(--card); border-radius: 14px;
            padding: 28px; margin-bottom: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(44,36,22,0.03);
        }
        .card-title {
            font-size: 16px; font-weight: 600; color: var(--ink);
            margin-bottom: 16px; padding-bottom: 12px;
            border-bottom: 2px solid rgba(180,148,80,0.15);
        }

        /* 表格 */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        table th, table td {
            padding: 12px 16px; text-align: left;
            border-bottom: 1px solid var(--border);
        }
        table th {
            background: rgba(180,148,80,0.04);
            font-weight: 600; color: var(--muted);
            font-size: 11px; text-transform: uppercase;
            letter-spacing: 0.06em; white-space: nowrap;
            border-bottom: 2px solid var(--border);
        }
        table td { color: var(--ink); }
        table tbody tr {
            transition: background 0.15s ease;
            animation: fadeInRow 0.4s ease forwards; opacity: 0;
        }
        table tbody tr:nth-child(1) { animation-delay: 0.04s; }
        table tbody tr:nth-child(2) { animation-delay: 0.07s; }
        table tbody tr:nth-child(3) { animation-delay: 0.10s; }
        table tbody tr:nth-child(4) { animation-delay: 0.13s; }
        table tbody tr:nth-child(5) { animation-delay: 0.16s; }
        table tbody tr:nth-child(6) { animation-delay: 0.19s; }
        table tbody tr:nth-child(7) { animation-delay: 0.22s; }
        table tbody tr:nth-child(8) { animation-delay: 0.25s; }
        table tbody tr:nth-child(9) { animation-delay: 0.28s; }
        table tbody tr:nth-child(10) { animation-delay: 0.31s; }
        table tbody tr:nth-child(11) { animation-delay: 0.34s; }
        table tbody tr:nth-child(12) { animation-delay: 0.37s; }
        table tbody tr:nth-child(13) { animation-delay: 0.40s; }
        table tbody tr:nth-child(14) { animation-delay: 0.43s; }
        table tbody tr:nth-child(15) { animation-delay: 0.46s; }
        @keyframes fadeInRow {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        table tbody tr:hover { background: rgba(180,148,80,0.03); }
        table a { color: var(--gold); text-decoration: none; }
        table a:hover { color: var(--wine); }

        /* 徽章 — 全圆药丸 */
        .badge {
            display: inline-block; padding: 3px 12px;
            border-radius: 999px; font-size: 11px;
            font-weight: 600; letter-spacing: 0.03em; white-space: nowrap;
        }
        .badge-yellow { background: rgba(180,148,80,0.1); color: #8b6914; }
        .badge-green  { background: rgba(74,103,65,0.1); color: var(--moss); }
        .badge-blue   { background: rgba(180,148,80,0.08); color: var(--gold); }
        .badge-red    { background: rgba(107,39,55,0.08); color: var(--wine); }
        .badge-gray   { background: rgba(140,132,120,0.08); color: var(--muted); }
        .badge-amber  { background: rgba(180,148,80,0.1); color: var(--gold); }

        /* 按钮 */
        .btn {
            display: inline-block; padding: 7px 16px;
            border-radius: 20px; font-size: 13px;
            text-decoration: none; border: 1px solid var(--border);
            background: var(--card); color: var(--muted);
            cursor: pointer; font-family: inherit;
            transition: all 0.2s cubic-bezier(0.25,0.1,0.25,1);
        }
        .btn:hover { background: rgba(180,148,80,0.04); border-color: var(--gold); color: var(--ink); }
        .btn-primary {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #fff; border-color: transparent;
            box-shadow: 0 2px 6px rgba(180,148,80,0.2);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--gold-light), #e0c990);
            box-shadow: 0 4px 12px rgba(180,148,80,0.3);
            transform: translateY(-1px); color: #fff;
        }
        .btn-danger { color: var(--wine); border-color: rgba(107,39,55,0.3); }
        .btn-danger:hover { background: rgba(107,39,55,0.06); border-color: var(--wine); }
        .btn-sm { padding: 4px 10px; font-size: 12px; border-radius: 16px; }

        /* 提示 */
        .alert {
            padding: 12px 16px; border-radius: 12px;
            margin-bottom: 20px; font-size: 14px;
        }
        .alert-success { background: rgba(74,103,65,0.08); color: var(--moss); border: 1px solid rgba(74,103,65,0.15); }
        .alert-error   { background: rgba(107,39,55,0.06); color: var(--wine); border: 1px solid rgba(107,39,55,0.15); }

        /* 表单 */
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; margin-bottom: 5px;
            font-size: 13px; color: var(--muted); font-weight: 500;
        }
        .form-control {
            width: 100%; height: 42px; padding: 0 14px;
            border: 1px solid var(--border); border-radius: 12px;
            font-size: 14px; outline: none; font-family: inherit;
            background: #fefdfb; color: var(--ink);
            transition: border-color 0.2s cubic-bezier(0.25,0.1,0.25,1),
                        box-shadow 0.2s cubic-bezier(0.25,0.1,0.25,1);
        }
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(180,148,80,0.12);
        }
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 50px #fefdfb inset;
            -webkit-text-fill-color: #2c2416;
            transition: background-color 9999s ease-in-out 0s;
        }
        select.form-control { cursor: pointer; }

        .flex-row { display: flex; gap: 14px; align-items: stretch; flex-wrap: wrap; }
        .flex-1 { flex: 1; }
        .text-right { text-align: right; }
        .text-muted { color: var(--muted); font-size: 13px; }

        /* 分页 */
        .pagination {
            display: flex; gap: 4px; justify-content: center;
            margin-top: 24px; font-size: 13px;
            list-style: none; padding: 0;
        }
        .pagination .page-link {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px; padding: 0 8px;
            border: 1px solid var(--border); border-radius: 20px;
            text-decoration: none; color: var(--muted);
            background: var(--card); transition: all 0.2s ease;
        }
        .pagination .page-link:hover { background: rgba(180,148,80,0.04); border-color: var(--gold); color: var(--gold); }
        .pagination .active .page-link {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #fff; border-color: transparent; font-weight: 600;
            box-shadow: 0 2px 8px rgba(180,148,80,0.25);
        }
        .pagination .disabled .page-link {
            color: #d4ccb8; cursor: not-allowed; pointer-events: none;
        }

        /* 统计卡片 */
        .stat-grid {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 16px; margin-bottom: 28px;
        }
        @media (max-width: 1024px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .stat-grid { grid-template-columns: 1fr; } }

        .stat-card {
            background: var(--card); border-radius: 16px;
            padding: 28px; border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(44,36,22,0.03);
            transition: transform 0.25s cubic-bezier(0.25,0.1,0.25,1),
                        box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .stat-card::before {
            content: ''; display: block;
            width: 32px; height: 3px; background: var(--gold);
            border-radius: 2px; margin-bottom: 20px;
        }
        a .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(44,36,22,0.08);
            border-color: rgba(180,148,80,0.25);
        }
        .stat-card .num {
            font-family: 'Cormorant Garamond', serif;
            font-size: 42px; font-weight: 700; color: var(--gold);
            letter-spacing: -0.02em; line-height: 1;
        }
        .stat-card .label {
            font-size: 13px; color: var(--muted); margin-top: 10px;
            font-weight: 500; text-transform: uppercase; letter-spacing: 0.04em;
        }
        .stat-card a { text-decoration: none; }

        .empty-state {
            text-align: center; padding: 48px 20px;
            color: var(--muted); font-size: 14px;
        }

        /* 工具 */
        .section-divider {
            border: none; height: 2px;
            background: linear-gradient(90deg, rgba(180,148,80,0.3), transparent);
            margin: 24px 0;
        }
        .card-accent {
            border-left: 4px solid var(--gold);
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="logo">
                <span class="icon">📚</span>
                Campus<span style="font-style:italic;color:var(--gold);">Books</span>
            </div>
            <nav class="nav">
                <a href="{{ url('admin/dashboard') }}" class="{{ request()->is('admin') || request()->is('admin/dashboard') ? 'active' : '' }}">✦ 仪表盘</a>
                <a href="{{ url('admin/reviews') }}" class="{{ request()->is('admin/reviews*') ? 'active' : '' }}">✦ 审核管理</a>
                <a href="{{ url('admin/books') }}" class="{{ request()->is('admin/books*') ? 'active' : '' }}">✦ 书籍管理</a>
                <a href="{{ url('admin/orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}">✦ 订单管理</a>
                <a href="{{ url('admin/users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">✦ 用户管理</a>
                <a href="{{ url('admin/wants') }}" class="{{ request()->is('admin/wants*') ? 'active' : '' }}">✦ 求购管理</a>
                <a href="{{ url('admin/categories') }}" class="{{ request()->is('admin/categories*') ? 'active' : '' }}">✦ 分类管理</a>
                <a href="{{ url('admin/colleges') }}" class="{{ request()->is('admin/colleges*') ? 'active' : '' }}">✦ 院校管理</a>
            </nav>
        </aside>

        <div class="main">
            <header class="topbar">
                <span class="site-name">校园二手书 · 管理后台</span>
                <div class="user-info">
                    <span>✦</span>
                    <span class="name">{{ Auth::user()->name }}</span>
                    <a href="{{ url('admin/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">退出</a>
                    <form id="logout-form" action="{{ url('admin/logout') }}" method="POST" style="display:none">@csrf</form>
                </div>
            </header>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
