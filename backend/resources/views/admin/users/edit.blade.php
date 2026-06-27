@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / <a href="{{ url('admin/users') }}">用户管理</a> / 编辑</div>
<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
    <a href="{{ url('admin/users') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">&larr; 返回用户管理</a>
    <span class="page-title" style="margin:0;">编辑用户 #{{ $user->id }}</span>
</div>

    @if(session('page_success'))
        <div style="background:linear-gradient(135deg,#4a6741,#5a7d51);color:#fff;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:500;">{{ session('page_success') }}</div>
    @endif

<div class="card" style="max-width:600px;margin:0 auto;">
    <form method="POST" action="{{ url('admin/users/'.$user->id.'/update') }}">
        @csrf
        <div class="form-group">
            <label>姓名</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="form-group">
            <label>学号</label>
            <input type="text" name="student_id" class="form-control" value="{{ old('student_id', $user->student_id) }}">
        </div>
        <div class="form-group">
            <label>手机号</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
        </div>
        <div class="form-group">
            <label>角色</label>
            <select name="role" class="form-control">
                <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>学生</option>
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>管理员</option>
            </select>
        </div>
        <div class="form-group">
            <label>状态</label>
            <select name="status" class="form-control">
                <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>正常</option>
                <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>已禁用</option>
            </select>
        </div>
        <div class="form-group">
            <label>新密码（留空则不修改）</label>
            <input type="text" name="password" class="form-control" placeholder="至少6位">
        </div>
        @if($errors->any())
            <div style="background:#fee2e2;color:#bc4742;padding:10px 14px;border-radius:6px;margin-bottom:16px;">
                @foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach
            </div>
        @endif
        <button type="submit" class="btn btn-primary">保存</button>
    </form>
</div>
@endsection
