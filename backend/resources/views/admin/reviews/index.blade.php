@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / 审核管理</div>
<div class="page-title">审核管理</div>

<div class="card">
    <form method="GET" class="flex-row" style="margin-bottom:16px">
        <select name="status" class="form-control" style="width:120px" onchange="this.form.submit()">
            <option value="">待审核</option>
            <option value="all" {{ request('status')=='all'?'selected':'' }}>全部</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>已通过</option>
            <option value="removed" {{ request('status')=='removed'?'selected':'' }}>已驳回</option>
        </select>
        <input type="text" name="keyword" class="form-control" placeholder="书名/作者/学号" value="{{ request('keyword') }}" style="width:200px">
        <button type="submit" class="btn btn-primary">搜索</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th><th>封面</th><th>书名</th><th>作者/出版社</th><th>成色</th><th>卖家</th><th>提交时间</th><th>状态</th><th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse($books as $book)
            <tr>
                <td>{{ $book->id }}</td>
                <td>
                    @php $cover = $book->images->where('type','cover')->first(); @endphp
                    @if($cover)
                        <img src="{{ asset('storage/'.$cover->path) }}" width="40" height="52" style="object-fit:cover;border-radius:2px">
                    @endif
                </td>
                <td><a href="{{ url('admin/reviews/'.$book->id) }}"><strong>{{ $book->title }}</strong></a></td>
                <td>{{ $book->author }} · {{ $book->publisher }}</td>
                <td>
                    @php $cond = ['like_new'=>'全新','excellent'=>'几乎全新','good'=>'正常使用','fair'=>'较旧']; @endphp
                    {{ $cond[$book->condition] ?? $book->condition }}
                </td>
                <td>{{ $book->seller->name ?? '-' }}<br><small class="text-muted">{{ $book->seller->student_id ?? '' }}</small></td>
                <td>{{ $book->submitted_at }}</td>
                <td>
                    @php
                        $badge = ['pending_review'=>'badge-yellow','approved'=>'badge-green','active'=>'badge-green','sold'=>'badge-blue','removed'=>'badge-gray'];
                        $label = ['pending_review'=>'待审核','approved'=>'已通过','active'=>'已入库','sold'=>'已售出','removed'=>'已驳回/下架'];
                    @endphp
                    <span class="badge {{ $badge[$book->status] ?? 'badge-gray' }}">{{ $label[$book->status] ?? $book->status }}</span>
                </td>
                <td>
                    @if($book->status === 'pending_review')
                        <a href="{{ url('admin/reviews/'.$book->id) }}" class="btn btn-primary btn-sm">审核</a>
                    @elseif($book->status === 'approved')
                        <button class="btn btn-primary btn-sm" onclick="receiveBook({{ $book->id }}, '{{ $book->title }}')">确认入库</button>
                    @else
                        <a href="{{ url('admin/reviews/'.$book->id) }}" class="btn btn-sm">查看</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;padding:40px;color:#999">暂无数据</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $books->appends(request()->query())->links() }}</div>
</div>

<script>
function receiveBook(id, title) {
    var price = prompt('售价（¥）：');
    if (!price) return;
    var cost = prompt('收书价（¥）：');
    if (!cost) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/reviews/' + id + '/receive';
    form.innerHTML = '@csrf<input name="price" value="' + price + '"><input name="cost_price" value="' + cost + '">';
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
