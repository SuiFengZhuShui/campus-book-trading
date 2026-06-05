@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / <a href="{{ url('admin/orders') }}">订单管理</a> / 订单详情</div>
<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
    <a href="{{ url('admin/orders') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">&larr; 返回订单管理</a>
    <span class="page-title" style="margin:0;">订单详情</span>
</div>

<div class="flex-row">
    <div class="flex-1" style="width:65%">
        <div class="card">
            <div class="card-title">
                订单号：{{ $order->order_no }}
                @php
                    $badge = ['pending'=>'badge-yellow','paid'=>'badge-blue','confirmed'=>'badge-cyan','picked_up'=>'badge-green','cancelled'=>'badge-red'];
                    $label = ['pending'=>'待付款','paid'=>'已付款','confirmed'=>'已确认','picked_up'=>'已完成','cancelled'=>'已取消'];
                @endphp
                <span class="badge {{ $badge[$order->status] ?? 'badge-gray' }}">{{ $label[$order->status] ?? $order->status }}</span>
            </div>
            <table>
                <thead><tr><th>封面</th><th>书名</th><th>售价</th><th>卖家</th><th>结算状态</th></tr></thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            @php $cover = $item->book ? $item->book->images->where('type','cover')->first() : null @endphp
                            @if($cover)<img src="{{ asset('storage/'.$cover->path) }}" width="40" height="52" style="object-fit:cover;border-radius:2px">@endif
                        </td>
                        <td>{{ $item->book->title ?? '-' }}</td>
                        <td>¥{{ $item->price }}</td>
                        <td>{{ $item->book->seller->name ?? '-' }}</td>
                        <td>
                            @if($item->book && $item->book->seller_paid)
                                <span class="badge badge-green">已结算</span>
                            @else
                                <span class="badge badge-gray">未结算</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <tr><td colspan="3"></td><td><strong>合计：</strong></td><td><strong>¥{{ $order->total_amount }}</strong></td></tr>
                </tbody>
            </table>
        </div>

        <div class="card">
            <div class="card-title">取书信息</div>
            <p>取书地点：{{ $order->pickup_location }}</p>
        </div>

        <div class="card">
            <div class="card-title">买家信息</div>
            <p>姓名：{{ $order->buyer->name ?? '-' }} &nbsp; 学号：{{ $order->buyer->student_id ?? '-' }} &nbsp; 手机号：{{ $order->buyer->phone ?? '-' }}</p>
        </div>
    </div>

    <div style="width:35%;display:flex;flex-direction:column;">
        <div class="card" style="flex:1;">
            <div class="card-title">订单时间线</div>
            @foreach($order->timeline as $t)
            <div style="padding:8px 0;border-bottom:1px solid #cec4b0">
                ● {{ $t->created_at }} &nbsp; {{ $t->remark }}
            </div>
            @endforeach
        </div>

        @if($order->status === 'paid')
        <div class="card">
            <button class="btn btn-primary" style="width:100%;margin-bottom:8px;padding:10px" onclick="confirmOrder({{ $order->id }})">确认订单</button>
            <button class="btn btn-danger" style="width:100%;padding:10px" onclick="cancelOrder({{ $order->id }})">取消并退款</button>
        </div>
        @endif
        @if($order->status === 'confirmed')
        <div class="card">
            <button class="btn btn-primary" style="width:100%;padding:10px" onclick="pickupOrder({{ $order->id }})">确认取书（结算卖家）</button>
        </div>
        @endif
    </div>
</div>

<script>
function confirmOrder(id) { if(!confirm('确认订单？')) return; var f=document.createElement('form');f.method='POST';f.action='/admin/orders/'+id+'/confirm';f.innerHTML='@csrf';document.body.appendChild(f);f.submit(); }
function pickupOrder(id) { if(!confirm('确认买家已取书？卖家将立即结算。')) return; var f=document.createElement('form');f.method='POST';f.action='/admin/orders/'+id+'/pickup';f.innerHTML='@csrf';document.body.appendChild(f);f.submit(); }
function cancelOrder(id) { var r=prompt('取消原因（必填）：'); if(!r) return; var f=document.createElement('form');f.method='POST';f.action='/admin/orders/'+id+'/cancel';f.innerHTML='@csrf<input type="hidden" name="reason" value="'+r+'">';document.body.appendChild(f);f.submit(); }
</script>
@endsection
