@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / <a href="{{ url('admin/ratings') }}">评价管理</a> / 详情</div>
<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
    <a href="{{ url('admin/ratings') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">&larr; 返回评价管理</a>
    <span class="page-title" style="margin:0;">评价详情 #{{ $review->id }}</span>
</div>

<div class="flex-row">
    <div class="flex-1" style="width:50%">
        <div class="card">
            <div class="card-title">评价信息</div>
            <table>
                <tr><td>评价人</td><td>{{ $review->user_name ?: '-' }}</td></tr>
                <tr><td>书况评分</td><td>{{ str_repeat('★', $review->book_rating) }}{{ str_repeat('☆', 5 - $review->book_rating) }}（{{ $review->book_rating }}/5）</td></tr>
                <tr><td>服务评分</td><td>{{ str_repeat('★', $review->service_rating) }}{{ str_repeat('☆', 5 - $review->service_rating) }}（{{ $review->service_rating }}/5）</td></tr>
                <tr><td>评价内容</td><td>{{ $review->comment ?: '无文字评价' }}</td></tr>
                <tr><td>评价时间</td><td>{{ $review->created_at }}</td></tr>
            </table>
        </div>
    </div>
    <div style="width:50%">
        <div class="card">
            <div class="card-title">关联订单</div>
            @if($review->order_no)
                @php $statusMap = ['pending'=>'待付款','paid'=>'已付款','confirmed'=>'已确认','picked_up'=>'已完成','cancelled'=>'已取消']; @endphp
                <p>订单号：{{ $review->order_no }}</p>
                <p>状态：{{ $statusMap[$review->order_status] ?? '-' }}</p>
                <p>金额：¥{{ $review->order_amount }}</p>
            @else
                <p style="color:#8c8478;">订单已删除</p>
            @endif
        </div>
        <div class="card">
            <div class="card-title">关联书籍</div>
            @if($review->book_title)
                @if($review->cover_path)
                <img src="{{ asset('storage/'.$review->cover_path) }}" style="width:80px;height:105px;object-fit:cover;border-radius:4px;margin-bottom:12px;">
                @endif
                <p>书名：{{ $review->book_title }}</p>
                <p>作者：{{ $review->book_author }}</p>
                <p>出版社：{{ $review->book_publisher }}</p>
            @else
                <p style="color:#8c8478;">书籍已删除</p>
            @endif
        </div>
    </div>
</div>
@endsection
