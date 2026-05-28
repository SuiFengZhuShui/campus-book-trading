@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / <a href="{{ url('admin/books') }}">书籍管理</a> / 编辑</div>
<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
    <a href="{{ url('admin/books') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">&larr; 返回书籍管理</a>
    <span class="page-title" style="margin:0;">编辑书籍</span>
</div>

<div class="flex-row" style="align-items:flex-start">
    <div class="flex-1" style="width:60%">
        <div class="card">
            <form method="POST" action="{{ url('admin/books/'.$book->id.'/update') }}">
                @csrf
                <div class="form-group"><label>书名</label><input type="text" name="title" class="form-control" value="{{ $book->title }}"></div>
                <div class="flex-row">
                    <div class="flex-1 form-group"><label>作者</label><input type="text" name="author" class="form-control" value="{{ $book->author }}"></div>
                    <div class="flex-1 form-group"><label>出版社</label><input type="text" name="publisher" class="form-control" value="{{ $book->publisher }}"></div>
                </div>
                <div class="flex-row">
                    <div class="flex-1 form-group"><label>ISBN</label><input type="text" name="isbn" class="form-control" value="{{ $book->isbn }}"></div>
                    <div class="flex-1 form-group">
                        <label>分类</label>
                        <select name="category_id" class="form-control">
                            @foreach(\App\Category::orderBy('sort')->get() as $cat)
                                <option value="{{ $cat->id }}" {{ $book->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex-row">
                    <div class="flex-1 form-group">
                        <label>成色</label>
                        <select name="condition" class="form-control">
                            <option value="like_new" {{ $book->condition=='like_new'?'selected':'' }}>全新</option>
                            <option value="excellent" {{ $book->condition=='excellent'?'selected':'' }}>几乎全新</option>
                            <option value="good" {{ $book->condition=='good'?'selected':'' }}>正常使用</option>
                            <option value="fair" {{ $book->condition=='fair'?'selected':'' }}>较旧</option>
                        </select>
                    </div>
                    <div class="flex-1 form-group"><label>原价</label><input type="number" step="0.01" name="original_price" class="form-control" value="{{ $book->original_price }}"></div>
                </div>
                <div class="flex-row">
                    <div class="flex-1 form-group"><label>售价</label><input type="number" step="0.01" name="price" class="form-control" value="{{ $book->price }}"></div>
                    <div class="flex-1 form-group"><label>收书价</label><input type="number" step="0.01" name="cost_price" class="form-control" value="{{ $book->cost_price }}"></div>
                </div>
                <button type="submit" class="btn btn-primary">保存修改</button>
                <a href="{{ url('admin/books') }}" class="btn">取消</a>
            </form>
        </div>
    </div>
    <div style="width:40%">
        <div class="card">
            <div class="card-title">图片</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                @foreach($book->images as $img)
                    <img src="{{ asset('storage/'.$img->path) }}" style="width:100px;height:130px;object-fit:cover;border-radius:4px">
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-title">卖家信息</div>
            <p>姓名：{{ $book->seller->name ?? '-' }}</p>
            <p>学号：{{ $book->seller->student_id ?? '-' }}</p>
            <p>手机号：{{ $book->seller->phone ?? '-' }}</p>
        </div>
        <div class="card">
            <div class="card-title">书籍状态</div>
            @php
                $label = ['pending_review'=>'待审核','approved'=>'已通过','active'=>'在售','sold'=>'已售出','removed'=>'已下架/驳回'];
            @endphp
            <p>状态：{{ $label[$book->status] ?? $book->status }}</p>
            @if($book->status === 'removed' && $book->reject_reason)
                <p style="color:#bc4742;">驳回/下架原因：{{ $book->reject_reason }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
