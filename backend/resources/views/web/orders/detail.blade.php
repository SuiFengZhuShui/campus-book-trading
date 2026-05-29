@extends('web.layouts.app')

@section('title', '订单详情 - 校园二手书网')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    @php $fromSells = request('from') === 'sells'; @endphp
    <a href="{{ $fromSells ? '/my-sells' : '/orders' }}" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; background: linear-gradient(135deg, #b49450, #d4bc7c); color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; margin-bottom: 20px;">&larr; {{ $fromSells ? '返回我的卖书' : '返回订单列表' }}</a>

    @php
        $statusMap = ['pending'=>'待付款','paid'=>'待确认','confirmed'=>'待取书','picked_up'=>'已完成','cancelled'=>'已取消'];
        $statusBg = ['pending'=>'#fef9f0','paid'=>'rgba(180,148,80,0.08)','confirmed'=>'#edf5f0','picked_up'=>'#f4f1ec','cancelled'=>'#f4f1ec'];
        $statusColor = ['pending'=>'#b0822c','paid'=>'#b49450','confirmed'=>'#2d6a4f','picked_up'=>'#8c8478','cancelled'=>'#8c8478'];
    @endphp

    {{-- 订单状态 --}}
    <div style="background: #fffdfa; border-radius: 12px; padding: 24px; border: 1px solid rgba(26,31,43,0.05); box-shadow: 0 2px 8px rgba(26,31,43,0.04); margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <div style="font-size: 13px; color: #8c8478; margin-bottom: 4px;">订单号：{{ $order->order_no }}</div>
                <span style="display: inline-block; padding: 6px 18px; border-radius: 999px; font-size: 14px; font-weight: 600; letter-spacing: 0.03em; background: {{ $statusBg[$order->status] ?? '#f4f1ec' }}; color: {{ $statusColor[$order->status] ?? '#8c8478' }};">
                    {{ $statusMap[$order->status] ?? $order->status }}
                </span>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 12px; color: #8c8478; margin-bottom: 4px;">订单金额</div>
                <div style="font-size: 28px; font-weight: 700; color: #b49450;">¥{{ $order->total_amount }}</div>
            </div>
        </div>

        @php
            $isSeller = $order->items->contains(function ($item) {
                return $item->book && $item->book->seller_id === auth()->id();
            });
        @endphp
        @if($order->buyer_id === auth()->id())
            @if($order->status === 'pending')
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <form method="POST" action="/orders/{{ $order->id }}/pay" style="flex: 1;">
                        @csrf
                        <button type="submit" style="width: 100%; height: 42px; background: linear-gradient(135deg, #b49450, #b49450); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer; transition: opacity 0.2s;">确认支付</button>
                    </form>
                    <form method="POST" action="/orders/{{ $order->id }}/cancel" style="flex: 1;" onsubmit="return confirm('确定取消订单？')">
                        @csrf
                        <button type="submit" style="width: 100%; height: 42px; background: #fffdfa; color: #8c8478; border: 1px solid #e5dccf; border-radius: 8px; font-size: 15px; cursor: pointer; transition: background 0.2s;">取消订单</button>
                    </form>
                </div>
            @endif
            @if($order->status === 'paid')
                <form method="POST" action="/orders/{{ $order->id }}/cancel" style="margin-top: 20px;" onsubmit="return confirm('确定取消订单？')">
                    @csrf
                    <button type="submit" style="height: 42px; padding: 0 24px; background: #fffdfa; color: #8c8478; border: 1px solid #e5dccf; border-radius: 8px; font-size: 15px; cursor: pointer; transition: background 0.2s;">取消订单</button>
                </form>
            @endif
        @endif
        @if(in_array($order->status, ['cancelled', 'picked_up']) && ($order->buyer_id === auth()->id() || $isSeller))
            <form method="POST" action="/orders/{{ $order->id }}/delete" style="margin-top: 20px;" onsubmit="return confirm('确定删除此订单吗？')">
                @csrf
                <button type="submit" style="height: 42px; padding: 0 24px; background: #fffdfa; color: #bc4742; border: 1px solid #f5c6cb; border-radius: 8px; font-size: 15px; cursor: pointer; transition: background 0.2s;">删除订单</button>
            </form>
        @endif
    </div>

    {{-- 书籍列表 --}}
    <div style="background: #fffdfa; border-radius: 12px; border: 1px solid rgba(26,31,43,0.05); box-shadow: 0 1px 2px rgba(26,31,43,0.03); margin-bottom: 20px; overflow: hidden;">
        <div style="padding: 16px 24px; font-size: 15px; font-weight: 600; color: #2c2416; border-bottom: 1px solid #e5dccf;">购买书籍</div>
        @foreach($order->items as $item)
            <div style="display: flex; gap: 16px; padding: 16px 24px; {{ !$loop->last ? 'border-bottom: 1px solid #e5dccf;' : '' }}">
                @php $cover = $item->book->images->where('type', 'cover')->first(); @endphp
                <a href="/books/{{ $item->book->id }}" style="flex-shrink: 0;">
                    @if($cover)
                        <img src="{{ asset('storage/' . $cover->path) }}" alt="{{ $item->book->title }}"
                             style="width: 64px; height: 90px; object-fit: cover; border-radius: 6px;">
                    @else
                        <div style="width: 64px; height: 90px; border-radius: 6px; background: #f4f1ec; display: flex; align-items: center; justify-content: center; font-size: 28px;">📖</div>
                    @endif
                </a>
                <div style="flex: 1;">
                    <a href="/books/{{ $item->book->id }}" style="font-size: 14px; font-weight: 500; color: #2c2416;">{{ $item->book->title }}</a>
                    <div style="font-size: 12px; color: #8c8478; margin-top: 4px;">{{ $item->book->author }} / {{ $item->book->publisher }}</div>
                </div>
                <div style="text-align: right; font-size: 16px; font-weight: 600; color: #b49450;">¥{{ $item->price }}</div>
            </div>
        @endforeach
    </div>

    {{-- 订单信息 --}}
    <div style="background: #fffdfa; border-radius: 12px; padding: 24px; border: 1px solid rgba(26,31,43,0.05); box-shadow: 0 1px 2px rgba(26,31,43,0.03); margin-bottom: 20px;">
        <div style="font-size: 15px; font-weight: 600; color: #2c2416; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid rgba(180,148,80,0.25);">订单信息</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px;">
            <div><span style="color: #8c8478;">取书地点：</span>{{ $order->pickup_location }}</div>
            <div><span style="color: #8c8478;">下单时间：</span>{{ $order->created_at->format('Y-m-d H:i') }}</div>
            @if($order->paid_at)
                <div><span style="color: #8c8478;">支付时间：</span>{{ $order->paid_at->format('Y-m-d H:i') }}</div>
            @endif
            @if($order->confirmed_at)
                <div><span style="color: #8c8478;">确认时间：</span>{{ $order->confirmed_at->format('Y-m-d H:i') }}</div>
            @endif
            @if($order->picked_up_at)
                <div><span style="color: #8c8478;">取书时间：</span>{{ $order->picked_up_at->format('Y-m-d H:i') }}</div>
            @endif
            @if($order->cancelled_at)
                <div><span style="color: #8c8478;">取消时间：</span>{{ $order->cancelled_at->format('Y-m-d H:i') }}</div>
                @if($order->cancel_reason)
                    <div><span style="color: #8c8478;">取消原因：</span>{{ $order->cancel_reason }}</div>
                @endif
            @endif
        </div>
    </div>

    {{-- 状态时间线 --}}
    @if($order->timeline->isNotEmpty())
        <div style="background: #fffdfa; border-radius: 12px; padding: 24px; border: 1px solid rgba(26,31,43,0.05); box-shadow: 0 1px 2px rgba(26,31,43,0.03);">
            <div style="font-size: 15px; font-weight: 600; color: #2c2416; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid rgba(180,148,80,0.25);">订单轨迹</div>
            <div style="position: relative; padding-left: 24px;">
                @foreach($order->timeline as $event)
                    <div style="position: relative; padding-bottom: {{ $loop->last ? '0' : '20px' }};">
                        <div style="position: absolute; left: -20px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: {{ $loop->first ? '#b49450' : '#d0cdc7' }}; border: 2px solid #fffdfa; box-shadow: {{ $loop->first ? '0 0 0 3px rgba(180,148,80,0.25)' : '0 0 0 2px #e5dccf' }};"></div>
                        @if(!$loop->last)
                            <div style="position: absolute; left: -16px; top: 18px; width: 2px; height: calc(100% - 4px); background: #e5dccf;"></div>
                        @endif
                        <div style="font-size: 14px; color: #2c2416;">{{ $event->remark }}</div>
                        <div style="font-size: 12px; color: #8c8478; margin-top: 2px;">{{ $event->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
