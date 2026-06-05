@extends('web.layouts.app')

@section('title', '发布求购 - 校园二手书网')

@section('content')
<div style="max-width: 640px; margin: 0 auto;">
    <a href="/wants" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:20px;">&larr; 返回求购广场</a>

    <div style="background:#ffffff;border-radius:12px;padding:28px;border:1px solid #cec4b0;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <h2 style="font-size:20px;font-weight:600;color:#2c2416;margin-bottom:24px;">发布求购</h2>

        @if(session('success'))
            <div style="background:#d4edda;color:#2d6a4f;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:14px;">{{ session('success') }}</div>
        @endif
        @if(session('warning'))
            <div style="background:#fff3cd;color:#856404;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:14px;">{!! session('warning') !!}</div>
        @endif

        <form id="post-want-form" action="/post-want" method="POST" onsubmit="return handleSubmit(event)">
            @csrf

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:#2c2416;"><span style="color:#bc4742;">*</span> 书名</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="请输入教材名称" required maxlength="200" style="width:100%;height:42px;padding:0 14px;border:1px solid #cec4b0;border-radius:8px;font-size:15px;">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:#2c2416;">作者</label>
                    <input type="text" name="author" value="{{ old('author') }}" placeholder="选填" maxlength="100" style="width:100%;height:42px;padding:0 14px;border:1px solid #cec4b0;border-radius:8px;font-size:15px;">
                </div>
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:#2c2416;">出版社</label>
                    <input type="text" name="publisher" value="{{ old('publisher') }}" placeholder="选填" maxlength="100" style="width:100%;height:42px;padding:0 14px;border:1px solid #cec4b0;border-radius:8px;font-size:15px;">
                </div>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:#2c2416;">学院</label>
                <select name="category_id" style="width:100%;height:42px;padding:0 14px;border:1px solid #cec4b0;border-radius:8px;font-size:15px;background:#fff;">
                    <option value="">请选择学院（选填）</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:#2c2416;"><span style="color:#bc4742;">*</span> 最高接受价</label>
                <input type="number" name="max_price" value="{{ old('max_price') }}" placeholder="¥" required min="0.01" step="0.01" style="width:100%;height:42px;padding:0 14px;border:1px solid #cec4b0;border-radius:8px;font-size:15px;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:#2c2416;">可接受成色（可多选，至少选一项）</label>
                <div style="display:flex;gap:16px;flex-wrap:wrap;">
                    @php
                        $checked = old('acceptable_condition') ? explode(',', old('acceptable_condition')) : [];
                    @endphp
                    @foreach(['like_new'=>'全新','excellent'=>'几乎全新','good'=>'正常使用','fair'=>'较旧'] as $val => $label)
                        <label style="font-weight:400;cursor:pointer;font-size:14px;">
                            <input type="checkbox" name="acceptable_condition[]" value="{{ $val }}" {{ in_array($val, $checked) ? 'checked' : '' }}> {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            @if($errors->any())
                <div style="background:#fee2e2;color:#bc4742;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:14px;">
                    @foreach($errors->all() as $err)
                        <p style="margin:0;">{{ $err }}</p>
                    @endforeach
                </div>
            @endif

            <button type="submit" style="width:100%;height:46px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:500;cursor:pointer;">发布求购</button>
            <p style="text-align:center;margin-top:12px;font-size:13px;color:#6e6559;">发布后有效期7天，到期自动过期</p>
        </form>
    </div>
</div>

<script>
function handleSubmit(e) {
    var checks = document.querySelectorAll('input[name="acceptable_condition[]"]:checked');
    if (checks.length === 0) {
        alert('请至少选一项可接受成色');
        return false;
    }
    return true;
}
</script>
@endsection
