@extends('admin.layouts.admin')

@section('content')
<div class="page-title">仪表盘</div>

<div class="stat-grid">
    <a href="{{ url('admin/reviews') }}">
        <div class="stat-card">
            <div class="num">{{ $pendingReview }}</div>
            <div class="label">待审核书籍</div>
        </div>
    </a>
    <a href="{{ url('admin/books') }}">
        <div class="stat-card">
            <div class="num">{{ $activeBooks }}</div>
            <div class="label">在售书籍</div>
        </div>
    </a>
    <a href="{{ url('admin/orders') }}">
        <div class="stat-card">
            <div class="num">{{ $pendingOrders }}</div>
            <div class="label">待处理订单</div>
        </div>
    </a>
    <div class="stat-card">
        <div class="num">{{ $todayOrders }}</div>
        <div class="label">今日订单</div>
    </div>
</div>

<div class="flex-row">
    <div class="flex-1">
        <div class="card">
            <div class="card-title">待审核书籍（最新5条）</div>
            @if($recentBooks->count())
            <div class="table-wrap">
            <table>
                <thead><tr><th>书名</th><th>卖家</th><th>提交时间</th></tr></thead>
                <tbody>
                    @foreach($recentBooks as $book)
                    <tr>
                        <td><a href="{{ url('admin/reviews/'.$book->id) }}">{{ $book->title }}</a></td>
                        <td>{{ $book->seller->name ?? '-' }}</td>
                        <td>{{ $book->submitted_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @else
            <p class="empty-state">暂无待审核书籍</p>
            @endif
        </div>
    </div>
    <div class="flex-1">
        <div class="card">
            <div class="card-title">待处理订单（最新5条）</div>
            @if($recentOrders->count())
            <div class="table-wrap">
            <table>
                <thead><tr><th>订单号</th><th>买家</th><th>金额</th></tr></thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td><a href="{{ url('admin/orders/'.$order->id) }}">{{ $order->order_no }}</a></td>
                        <td>{{ $order->buyer->name ?? '-' }}</td>
                        <td>¥{{ $order->total_amount }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            @else
            <p class="empty-state">暂无待处理订单</p>
            @endif
        </div>
    </div>
</div>
@endsection
