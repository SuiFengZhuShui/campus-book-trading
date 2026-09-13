@extends('admin.layouts.admin')

@section('content')
<div class="page-title">仪表盘</div>

<style>
    .spark { height: 52px; margin-top: 16px; }
</style>

<div class="stat-grid">
    <a href="{{ url('admin/reviews') }}">
        <div class="stat-card">
            <div class="num">{{ $pendingReview }}</div>
            <div class="label">待审核书籍</div>
            <div class="spark" id="spark-submit"></div>
        </div>
    </a>
    <a href="{{ url('admin/books') }}">
        <div class="stat-card">
            <div class="num">{{ $activeBooks }}</div>
            <div class="label">在售书籍</div>
            <div class="spark" id="spark-approve"></div>
        </div>
    </a>
    <a href="{{ url('admin/orders') }}">
        <div class="stat-card">
            <div class="num">{{ $pendingOrders }}</div>
            <div class="label">待处理订单</div>
            <div class="spark" id="spark-paid"></div>
        </div>
    </a>
    <div class="stat-card">
        <div class="num">{{ $todayOrders }}</div>
        <div class="label">今日订单</div>
        <div class="spark" id="spark-order"></div>
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
<script src="{{ asset('js/echarts.min.js') }}"></script>
<script>
(function () {
    var dates = @json($submitTrend['dates']);
    var series = {
        submit:  { values: @json($submitTrend['values']),  color: '#b49450' },
        approve: { values: @json($approveTrend['values']), color: '#4a6741' },
        paid:    { values: @json($paidTrend['values']),    color: '#6b2737' },
        order:   { values: @json($orderTrend['values']),   color: '#8b6914' }
    };
    Object.keys(series).forEach(function (key) {
        var el = document.getElementById('spark-' + key);
        if (!el || typeof echarts === 'undefined') return;
        var chart = echarts.init(el);
        var color = series[key].color;
        chart.setOption({
            grid: { left: 0, right: 0, top: 4, bottom: 0 },
            xAxis: { type: 'category', show: false, data: dates, boundaryGap: false },
            yAxis: { type: 'value', show: false, min: 0 },
            series: [{
                type: 'line',
                data: series[key].values,
                smooth: true,
                symbol: 'none',
                lineStyle: { width: 2, color: color },
                areaStyle: { color: color, opacity: 0.12 }
            }],
            tooltip: { trigger: 'axis', confine: true }
        });
    });
})();
</script>
@endsection
