@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / 书籍管理</div>
<div class="page-title">书籍管理</div>

<div class="card">
    <form method="GET" class="flex-row" style="margin-bottom:16px">
        <select name="status" class="form-control" style="width:130px" onchange="this.form.submit()">
            <option value="">全部</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>在售</option>
            <option value="sold" {{ request('status')=='sold'?'selected':'' }}>已售出</option>
            <option value="pending_review" {{ request('status')=='pending_review'?'selected':'' }}>待审核</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>已通过(未入库)</option>
            <option value="removed" {{ request('status')=='removed'?'selected':'' }}>已下架/驳回</option>
        </select>
        <input type="text" name="keyword" class="form-control" placeholder="书名/作者/ISBN" value="{{ request('keyword') }}" style="width:200px">
        <button type="submit" class="btn btn-primary">搜索</button>
    </form>

    <table>
        <thead>
            <tr><th>ID</th><th>封面</th><th>书名</th><th>作者/出版社</th><th>售价</th><th>收书价</th><th>成色</th><th>卖家</th><th>状态</th><th>操作</th></tr>
        </thead>
        <tbody>
            @php $cond = ['like_new'=>'全新','excellent'=>'几乎全新','good'=>'正常使用','fair'=>'较旧']; @endphp
            @forelse($books as $book)
            <tr>
                <td>{{ $book->id }}</td>
                <td>
                    @php $cover = $book->images->where('type','cover')->first(); @endphp
                    @if($cover)<img src="{{ asset('storage/'.$cover->path) }}" width="40" height="52" style="object-fit:cover;border-radius:2px">@endif
                </td>
                <td><strong>{{ $book->title }}</strong></td>
                <td>{{ $book->author }} · {{ $book->publisher }}</td>
                <td style="color:#2d6a4f;font-weight:600">{{ $book->price !== null ? '¥'.$book->price : '-' }}</td>
                <td class="text-muted">{{ $book->cost_price !== null ? '¥'.$book->cost_price : '-' }}</td>
                <td>{{ $cond[$book->condition] ?? $book->condition }}</td>
                <td>{{ $book->seller->name ?? '-' }}</td>
                <td>
                    @php
                        $badge = ['pending_review'=>'badge-yellow','approved'=>'badge-cyan','active'=>'badge-green','sold'=>'badge-blue','removed'=>'badge-gray'];
                        $label = ['pending_review'=>'待审核','approved'=>'已通过','active'=>'在售','sold'=>'已售出','removed'=>'已下架/驳回'];
                    @endphp
                    <span class="badge {{ $badge[$book->status] ?? 'badge-gray' }}">{{ $label[$book->status] ?? $book->status }}</span>
                    @if($book->status === 'removed' && $book->reject_reason)
                        <div style="font-size:11px;color:#bc4742;margin-top:2px">{{ $book->reject_reason }}</div>
                    @endif
                </td>
                <td>
                    @if($book->status === 'active')
                        <a href="{{ url('admin/books/'.$book->id.'/edit') }}" class="btn btn-sm">编辑</a>
                        <button class="btn btn-danger btn-sm" onclick="if(confirm('确认下架？')){var f=document.createElement('form');f.method='POST';f.action='{{ url('admin/books/'.$book->id.'/remove') }}';f.innerHTML='<input type=hidden name=_token value={{ csrf_token() }}>';document.body.appendChild(f);f.submit();}">下架</button>
                    @else
                        <a href="{{ url('admin/books/'.$book->id.'/edit') }}" class="btn btn-sm">查看</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:40px;color:#999">暂无数据</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $books->appends(request()->query())->links() }}</div>
</div>
@endsection
