@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / <a href="{{ url('admin/wants') }}">求购管理</a> / 编辑求购</div>
<div class="page-title">编辑求购 #{{ $want->id }}</div>

<a href="{{ url('admin/wants') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#c7915c,#d4a574);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:20px;">&larr; 返回求购管理</a>

    @if(session('page_success'))
        <div style="background:linear-gradient(135deg,#4a6741,#5a7d51);color:#fff;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:500;">{{ session('page_success') }}</div>
    @endif

<div class="card">
    <form method="POST" action="{{ url('admin/wants/'.$want->id.'/update') }}">
        @csrf
        @if($errors->any())
            <div class="alert alert-error" style="background:linear-gradient(135deg,#6b2737,#8b3a4a);color:#fff;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:500;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <div class="form-group">
            <label>书名</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $want->title) }}" required>
        </div>
        <div class="flex-row">
            <div class="form-group flex-1">
                <label>作者</label>
                <input type="text" name="author" class="form-control" value="{{ old('author', $want->author) }}">
            </div>
            <div class="form-group flex-1">
                <label>出版社</label>
                <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $want->publisher) }}">
            </div>
        </div>
        <div class="flex-row">
            <div class="form-group flex-1">
                <label>学院</label>
                <select name="category_id" class="form-control">
                    <option value="">请选择</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $want->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group flex-1">
                <label>最高价</label>
                <input type="number" name="max_price" class="form-control" step="0.01" value="{{ old('max_price', $want->max_price) }}" required>
            </div>
        </div>
        <div class="form-group">
            <label>成色要求</label>
            @php
                $condMap = ['like_new'=>'全新','excellent'=>'几乎全新','good'=>'正常使用','fair'=>'较旧'];
                $rawCond = old('acceptable_condition', $want->acceptable_condition);
                if (is_array($rawCond)) { $selectedConds = $rawCond; }
                else { $selectedConds = array_map('trim', explode(',', $rawCond)); }
            @endphp
            <div style="display:flex;gap:16px;flex-wrap:wrap;padding-top:4px;">
                @foreach($condMap as $key => $label)
                <label style="display:flex;align-items:center;gap:6px;font-size:14px;cursor:pointer;">
                    <input type="checkbox" name="acceptable_condition[]" value="{{ $key }}" {{ in_array($key, $selectedConds) ? 'checked' : '' }} style="accent-color:#b49450;">
                    {{ $label }}
                </label>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label>状态</label>
            <select name="status" class="form-control" style="width:160px">
                <option value="active" {{ old('status', $want->status) == 'active' ? 'selected' : '' }}>进行中</option>
                <option value="closed" {{ old('status', $want->status) == 'closed' ? 'selected' : '' }}>已关闭</option>
                <option value="expired" {{ old('status', $want->status) == 'expired' ? 'selected' : '' }}>已过期</option>
                <option value="closed" {{ old('status', $want->status) == 'closed' ? 'selected' : '' }}>已关闭</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">保存修改</button>
    </form>
</div>
@endsection
