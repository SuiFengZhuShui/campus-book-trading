@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / 评价管理</div>
<div class="page-title">评价管理</div>

<div class="card">
    <table>
        <thead>
            <tr><th>ID</th><th>评价人</th><th>书籍</th><th>书况评分</th><th>服务评分</th><th>评价内容</th><th>时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            @forelse($reviews as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->user_name ?: '-' }}</td>
                <td>{{ $r->book_title ?: '-' }}</td>
                <td>{{ str_repeat('★', $r->book_rating) }}{{ str_repeat('☆', 5 - $r->book_rating) }}</td>
                <td>{{ str_repeat('★', $r->service_rating) }}{{ str_repeat('☆', 5 - $r->service_rating) }}</td>
                <td>{{ \Illuminate\Support\Str::limit($r->comment, 30) }}</td>
                <td>{{ $r->created_at }}</td>
                <td><a href="{{ url('admin/ratings/'.$r->id) }}" class="btn btn-sm">详情</a></td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:40px;color:#999">暂无评价</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $reviews->links() }}</div>
</div>
@endsection
