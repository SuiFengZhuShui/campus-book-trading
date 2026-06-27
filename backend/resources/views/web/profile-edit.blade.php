@extends('web.layouts.app')

@section('title', '修改信息 - 校园二手书网')

@section('content')
<a href="/profile" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:24px;">&larr; 返回个人中心</a>

<div style="max-width: 480px; margin: 0 auto;">
    <form id="edit-profile-form" method="POST" action="/profile/update">
        @csrf
        <div style="background: #fff; border-radius: 14px; border: 1px solid #e5dccf; padding: 32px;">
            <div style="font-size: 18px; color: #2c2416; font-weight: 600; margin-bottom: 24px;">修改个人信息</div>

            <div style="margin-bottom: 18px;">
                <label style="display:block;font-size:14px;color:#2c2416;font-weight:500;margin-bottom:6px;">姓名 <span style="color:#bc4742;">*</span></label>
                <input name="name" value="{{ old('name', $user->name) }}" required maxlength="50" style="width:100%;height:44px;border:1px solid #c9c0ae;border-radius:8px;padding:0 12px;font-size:14px;background:#fff;box-sizing:border-box;">
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display:block;font-size:14px;color:#2c2416;font-weight:500;margin-bottom:6px;">学号 <span style="color:#bc4742;">*</span></label>
                <input name="student_id" value="{{ old('student_id', $user->student_id) }}" required maxlength="20" style="width:100%;height:44px;border:1px solid #c9c0ae;border-radius:8px;padding:0 12px;font-size:14px;background:#fff;box-sizing:border-box;">
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display:block;font-size:14px;color:#2c2416;font-weight:500;margin-bottom:6px;">手机号 <span style="color:#bc4742;">*</span></label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" required maxlength="11" style="width:100%;height:44px;border:1px solid #c9c0ae;border-radius:8px;padding:0 12px;font-size:14px;background:#fff;box-sizing:border-box;">
            </div>

            <button type="submit" style="width:100%;padding:14px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:500;cursor:pointer;margin-top:8px;">保存</button>
        </div>
    </form>
</div>

@if($errors->any())
<div style="max-width:480px;margin:16px auto 0;padding:14px 16px;background:linear-gradient(135deg,#6b2737,#8b3a4a);border-radius:8px;color:#fff;font-size:14px;font-weight:500;">
    @foreach($errors->all() as $e)
        <div>{{ $e }}</div>
    @endforeach
</div>
@endif
@endsection
