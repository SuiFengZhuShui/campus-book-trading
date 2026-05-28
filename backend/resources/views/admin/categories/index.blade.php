@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / 分类管理</div>
<div class="page-title">分类管理</div>

<div class="card">
    <form method="POST" action="{{ url('admin/categories') }}" class="flex-row" style="margin-bottom:16px">
        @csrf
        <input type="text" name="name" class="form-control" placeholder="新分类名称" style="width:200px" required>
        <button type="submit" class="btn btn-primary">+ 新增分类</button>
    </form>

    <table>
        <thead><tr><th>排序</th><th>ID</th><th>分类名称</th><th>关联书籍数</th><th>创建时间</th><th>操作</th></tr></thead>
        <tbody>
            @foreach($categories as $cat)
            <tr>
                <td>{{ $cat->sort }}</td>
                <td>{{ $cat->id }}</td>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->books_count }}</td>
                <td>{{ $cat->created_at }}</td>
                <td>
                    <a href="{{ url('admin/categories/'.$cat->id.'/edit') }}" class="btn btn-sm">编辑</a>
                    <form method="POST" action="{{ url('admin/categories/'.$cat->id.'/delete') }}" style="display:inline" onsubmit="return confirm('确认删除？')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" @if($cat->books_count > 0) disabled title="有{{ $cat->books_count }}本书，无法删除" @endif>删除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
