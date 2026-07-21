@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / 订单管理</div>
<div class="page-title">订单管理</div>

<div class="card">
    <form method="GET" class="flex-row" style="margin-bottom:16px">
        <select name="status" class="form-control" style="width:120px" onchange="this.form.submit()">
            <option value="">全部</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>待付款</option>
            <option value="paid" {{ request('status')=='paid'?'selected':'' }}>已付款</option>
            <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>已确认</option>
            <option value="picked_up" {{ request('status')=='picked_up'?'selected':'' }}>已完成</option>
            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>已取消</option>
        </select>
        <input type="text" name="keyword" class="form-control" placeholder="订单号/买家姓名" value="{{ request('keyword') }}" style="width:200px">
        <button type="submit" class="btn btn-primary">搜索</button>
    </form>

    <table>
        <thead>
            <tr><th>订单号</th><th>买家</th><th>商品数</th><th>金额</th><th>取书地点</th><th>状态</th><th>下单时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_no }}</td>
                <td>{{ $order->buyer->name ?? '-' }}<br><small class="text-muted">{{ $order->buyer->student_id ?? '' }}</small></td>
                <td>{{ $order->items->count() }}</td>
                <td><strong>¥{{ $order->total_amount }}</strong></td>
                <td>{{ Str::limit($order->pickup_location, 15) }}</td>
                <td>
                    @php
                        $badge = ['pending'=>'badge-yellow','paid'=>'badge-blue','confirmed'=>'badge-cyan','picked_up'=>'badge-green','cancelled'=>'badge-red'];
                        $label = ['pending'=>'待付款','paid'=>'已付款','confirmed'=>'已确认','picked_up'=>'已完成','cancelled'=>'已取消'];
                    @endphp
                    <span class="badge {{ $badge[$order->status] ?? 'badge-gray' }}">{{ $label[$order->status] ?? $order->status }}</span>
                </td>
                <td>{{ $order->created_at }}</td>
                <td>
                    @if($order->status === 'paid')
                        <button class="btn btn-primary btn-sm" onclick="confirmOrder({{ $order->id }}, '{{ $order->order_no }}')">确认</button>
                        <button class="btn btn-danger btn-sm" onclick="cancelOrder({{ $order->id }}, '{{ $order->order_no }}')">取消</button>
                    @endif
                    <a href="{{ url('admin/orders/'.$order->id) }}" class="btn btn-sm">查看</a>
                    @if(in_array($order->status, ['cancelled', 'picked_up']))
                        <button class="btn btn-sm" style="color:#bc4742;border-color:rgba(107,39,55,0.3)" onclick="deleteOrder({{ $order->id }}, '{{ $order->order_no }}')">删除</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:40px;color:#999">暂无订单</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $orders->appends(request()->query())->links() }}</div>
</div>

<script>
function confirmOrder(id, no) {
    if (!confirm('确认订单 ' + no + ' ？')) return;
    var form = document.createElement('form');
    form.method = 'POST'; form.action = '/admin/orders/' + id + '/confirm';
    form.innerHTML = '@csrf'; document.body.appendChild(form); form.submit();
}
function cancelOrder(id, no) {
    var reason = prompt('取消原因（必填）：');
    if (!reason) return;
    var form = document.createElement('form');
    form.method = 'POST'; form.action = '/admin/orders/' + id + '/cancel';
    form.innerHTML = '@csrf';
    var input = document.createElement('input');
    input.type = 'hidden'; input.name = 'reason'; input.value = reason;
    form.appendChild(input);
    document.body.appendChild(form); form.submit();
}
function deleteOrder(id, no) {
    if (!confirm('确定删除订单 ' + no + ' 吗？')) return;
    var form = document.createElement('form');
    form.method = 'POST'; form.action = '/admin/orders/' + id + '/delete';
    form.innerHTML = '@csrf';
    document.body.appendChild(form); form.submit();
}
</script>
@endsection
