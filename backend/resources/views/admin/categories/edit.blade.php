@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / <a href="{{ url('admin/categories') }}">分类管理</a> / 编辑</div>
<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
    <a href="{{ url('admin/categories') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">&larr; 返回分类管理</a>
    <span class="page-title" style="margin:0;">编辑分类</span>
</div>

<div class="card" style="max-width:500px;">
    <form method="POST" action="{{ url('admin/categories/'.$category->id) }}">
        @csrf
        <div class="form-group">
            <label>分类名称</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>
        </div>
        @if($errors->any())
            <div style="background:#fee2e2;color:#bc4742;padding:10px 14px;border-radius:6px;margin-bottom:16px;">
                @foreach($errors->all() as $e)<p style="margin:0;">{{ $e }}</p>@endforeach
            </div>
        @endif
        <div style="display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">保存</button>
        </div>
    </form>
</div>
@endsection
