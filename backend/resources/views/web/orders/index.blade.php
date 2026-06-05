@extends('web.layouts.app')

@section('title', '我的订单 - 校园二手书网')

@section('content')
<h2 style="font-size: 22px; font-weight: 600; color: #2c2416; letter-spacing: -0.01em; margin-bottom: 20px;">我的订单</h2>

@if($orders->count() > 0)
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @foreach($orders as $order)
            <a href="/orders/{{ $order->id }}" style="display: block; background: #ffffff; border-radius: 12px; padding: 20px 24px; border: 1px solid #cec4b0; box-shadow: 0 1px 2px rgba(0,0,0,0.04); border-left: 3px solid transparent; transition: box-shadow 0.25s, border-left-color 0.25s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                    <div style="flex: 1; min-width: 200px;">
                        <div style="font-size: 13px; color: #6e6559; margin-bottom: 6px;">订单号：{{ $order->order_no }}</div>
                        <div style="font-size: 15px; color: #2c2416; font-weight: 500;">
                            {{ $order->items->first()->book->title ?? '未知书籍' }}
                            @if($order->items->count() > 1)
                                <span style="font-size: 13px; color: #6e6559;">等{{ $order->items->count() }}本书</span>
                            @endif
                        </div>
                    </div>
                    <div style="text-align: right;">
                        @php
                            $statusMap = ['pending'=>'待付款','paid'=>'待确认','confirmed'=>'待取书','picked_up'=>'已完成','cancelled'=>'已取消'];
                            $statusBg = ['pending'=>'#fef9f0','paid'=>'rgba(180,148,80,0.08)','confirmed'=>'#edf5f0','picked_up'=>'#f4f1ec','cancelled'=>'#f4f1ec'];
                            $statusColor = ['pending'=>'#b0822c','paid'=>'#b49450','confirmed'=>'#2d6a4f','picked_up'=>'#6e6559','cancelled'=>'#6e6559'];
                        @endphp
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; letter-spacing: 0.03em; background: {{ $statusBg[$order->status] ?? '#f4f1ec' }}; color: {{ $statusColor[$order->status] ?? '#6e6559' }};">
                            {{ $statusMap[$order->status] ?? $order->status }}
                        </span>
                        <div style="font-size: 18px; font-weight: 700; color: #b49450; margin-top: 6px;">¥{{ $order->total_amount }}</div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="pagination">
        {{ $orders->links() }}
    </div>
@else
    <div class="no-books">
        <div class="icon">📋</div>
        <p style="font-size: 16px;">暂无订单</p>
        <p style="margin-top: 8px;">去<a href="/" style="color: #b49450;">首页</a>挑选心仪的课本吧</p>
    </div>
@endif
@endsection
