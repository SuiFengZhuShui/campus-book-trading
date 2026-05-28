<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 — 校园二手书</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Microsoft YaHei', sans-serif;
            background: linear-gradient(160deg, #1a1510 0%, #2c2416 60%, #3d3020 100%);
            background-image:
                linear-gradient(160deg, #1a1510 0%, #2c2416 60%, #3d3020 100%),
                repeating-linear-gradient(45deg, transparent, transparent 24px, rgba(255,255,255,0.012) 24px, rgba(255,255,255,0.012) 26px);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-wrapper { width: 440px; padding: 20px; }

        .brand { text-align: center; margin-bottom: 32px; color: #fff; }
        .brand .icon { font-size: 48px; margin-bottom: 12px; }
        .brand h1 { font-size: 26px; font-weight: 600; letter-spacing: 0.04em; }
        .brand p { font-size: 13px; opacity: 0.7; margin-top: 6px; letter-spacing: 0.06em; }
        .brand .accent-line { width: 40px; height: 2px; background: #b49450; margin: 14px auto 0; border-radius: 1px; }

        .card { background: #fffdfa; border-radius: 14px; padding: 40px 36px; box-shadow: 0 24px 64px rgba(0,0,0,0.2); }
        .card .title { text-align: center; font-size: 18px; font-weight: 600; color: #2c2416; letter-spacing: 0.02em; margin-bottom: 8px; }
        .card .subtitle { text-align: center; font-size: 13px; color: #8c8478; margin-bottom: 24px; }

        .form-group { margin-bottom: 16px; position: relative; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: #8c8478; margin-bottom: 6px; }
        .form-group .input-wrap { position: relative; }
        .form-group .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px; color: #bbb; pointer-events: none; }
        .form-control {
            width: 100%; height: 42px; padding: 0 14px 0 40px;
            border: 1px solid #e5dccf; border-radius: 8px; font-size: 14px;
            color: #2c2416; background: #fefdfb; outline: none;
            transition: border-color 0.2s cubic-bezier(0.25,0.1,0.25,1), box-shadow 0.2s cubic-bezier(0.25,0.1,0.25,1);
        }
        .form-control:focus { border-color: #b49450; box-shadow: 0 0 0 3px rgba(180,148,80,0.12); background: #fffdfa; }

        .btn {
            width: 100%; height: 46px;
            background: linear-gradient(135deg, #b49450, #d4bc7c);
            color: #fff; border: none; border-radius: 10px;
            font-size: 16px; font-weight: 600; cursor: pointer;
            letter-spacing: 0.06em;
            box-shadow: 0 4px 14px rgba(180,148,80,0.3);
            transition: transform 0.15s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            margin-top: 8px;
        }
        .btn:hover { opacity: 0.93; }
        .btn:active { transform: scale(0.98); }

        .alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        .alert-error { background: #fdf2f1; border: 1px solid rgba(188,71,66,0.2); color: #bc4742; }

        .links { display: flex; justify-content: center; gap: 24px; margin-top: 20px; }
        .links a { font-size: 13px; color: #8c8478; text-decoration: none; transition: color 0.2s; }
        .links a:hover { color: #b49450; }

        .bottom-text { text-align: center; margin-top: 24px; font-size: 12px; color: rgba(255,255,255,0.5); letter-spacing: 0.04em; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="brand">
            <div class="icon">📚</div>
            <h1>校园二手书</h1>
            <p>学生注册</p>
            <div class="accent-line"></div>
        </div>

        <div class="card">
            <div class="title">注册</div>
            <p class="subtitle">创建您的校园二手书账号</p>

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="/register">
                @csrf
                <div class="form-group">
                    <label>学号</label>
                    <div class="input-wrap">
                        <span class="input-icon">🎓</span>
                        <input type="text" name="student_id" class="form-control" value="{{ old('student_id') }}" placeholder="请输入学号" required maxlength="20">
                    </div>
                    @error('student_id')<div style="color:#bc4742;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>姓名</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="请输入姓名" required maxlength="50">
                    </div>
                    @error('name')<div style="color:#bc4742;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>手机号</label>
                    <div class="input-wrap">
                        <span class="input-icon">📱</span>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="请输入11位手机号" required maxlength="11">
                    </div>
                    @error('phone')<div style="color:#bc4742;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" class="form-control" placeholder="至少8位，含大小写字母和数字" required>
                    </div>
                    @error('password')<div style="color:#bc4742;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>确认密码</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="请再次输入密码" required>
                    </div>
                </div>
                <button type="submit" class="btn">注 册</button>
            </form>

            <div class="links">
                <a href="/login">已有账号？立即登录</a>
                <a href="/">返回首页</a>
            </div>
        </div>

        <div class="bottom-text">
            校园二手书交易平台 · <a href="/admin/login" style="color: rgba(255,255,255,0.4);">管理后台</a>
        </div>
    </div>
</body>
</html>
