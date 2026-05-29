@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / 求购管理</div>
<div class="page-title">求购管理</div>

<div class="card">
    <form method="GET" class="flex-row" style="margin-bottom:16px">
        <select name="status" class="form-control" style="width:120px" onchange="this.form.submit()">
            <option value="">全部状态</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>进行中</option>
            <option value="fulfilled" {{ request('status')=='fulfilled'?'selected':'' }}>已满足</option>
            <option value="expired" {{ request('status')=='expired'?'selected':'' }}>已过期</option>
            <option value="closed" {{ request('status')=='closed'?'selected':'' }}>已关闭</option>
        </select>
        <input type="text" name="keyword" class="form-control" placeholder="书名/发布者" value="{{ request('keyword') }}" style="width:200px">
        <button type="submit" class="btn btn-primary">搜索</button>
    </form>

    <table>
        <thead>
            <tr><th>ID</th><th>求购书名</th><th>学院</th><th>作者/出版社</th><th>最高价</th><th>成色要求</th><th>发布者</th><th>接单数</th><th>状态</th><th>有效期</th><th>操作</th></tr>
        </thead>
        <tbody>
            @forelse($wants as $want)
            <tr>
                <td>{{ $want->id }}</td>
                <td><strong>{{ $want->title }}</strong></td>
                <td>{{ $want->category->name ?? '—' }}</td>
                <td>{{ $want->author ?: '—' }} / {{ $want->publisher ?: '—' }}</td>
                <td>¥{{ $want->max_price }}</td>
                <td>{{ $want->acceptable_condition ? implode('、', array_map(function($v) { return ['like_new'=>'全新','excellent'=>'几乎全新','good'=>'正常使用','fair'=>'较旧'][trim($v)] ?? trim($v); }, explode(',', $want->acceptable_condition))) : '—' }}</td>
                <td>{{ $want->user->name ?? '-' }}</td>
                <td>{{ $want->fulfillments_count }}</td>
                <td>
                    @php
                        $badge = ['active'=>'badge-green','fulfilled'=>'badge-blue','expired'=>'badge-gray','closed'=>'badge-red'];
                        $label = ['active'=>'进行中','fulfilled'=>'已满足','expired'=>'已过期','closed'=>'已关闭'];
                    @endphp
                    <span class="badge {{ $badge[$want->status] ?? 'badge-gray' }}">{{ $label[$want->status] ?? $want->status }}</span>
                </td>
                <td>{{ $want->expires_at }}</td>
                <td style="white-space:nowrap">
                    <a href="{{ url('admin/wants/'.$want->id.'/edit') }}" class="btn btn-sm">编辑</a>
                    <form action="{{ url('admin/wants/'.$want->id.'/delete') }}" method="POST" onsubmit="return confirm('确定要删除这条求购吗？相关的接单记录也会一并删除。')" style="display:inline;margin-left:4px">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">删除</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="11" style="text-align:center;padding:40px;color:#999">暂无求购</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $wants->appends(request()->query())->links() }}</div>
</div>
@endsection
