@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / 用户管理</div>
<div class="page-title">用户管理</div>

<div class="card">
    <form method="GET" class="flex-row" style="margin-bottom:16px">
        <select name="role" class="form-control" style="width:120px" onchange="this.form.submit()">
            <option value="">全部角色</option>
            <option value="student" {{ request('role')=='student'?'selected':'' }}>学生</option>
            <option value="admin" {{ request('role')=='admin'?'selected':'' }}>管理员</option>
        </select>
        <select name="status" class="form-control" style="width:120px" onchange="this.form.submit()">
            <option value="">全部状态</option>
            <option value="1" {{ request('status')==='1'?'selected':'' }}>正常</option>
            <option value="0" {{ request('status')==='0'?'selected':'' }}>已禁用</option>
        </select>
        <input type="text" name="keyword" class="form-control" placeholder="姓名/学号/手机号" value="{{ request('keyword') }}" style="width:200px">
        <button type="submit" class="btn btn-primary">搜索</button>
    </form>

    <table>
        <thead>
            <tr><th>ID</th><th>姓名</th><th>学号</th><th>手机号</th><th>角色</th><th>状态</th><th>卖出数</th><th>购买数</th><th>注册时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->student_id ?: '—' }}</td>
                <td>{{ $user->phone }}</td>
                <td><span class="badge {{ $user->role=='admin'?'badge-red':'badge-blue' }}">{{ $user->role=='admin'?'管理员':'学生' }}</span></td>
                <td><span class="badge {{ $user->status?'badge-green':'badge-red' }}">{{ $user->status?'正常':'已禁用' }}</span></td>
                <td>{{ $user->selling_books_count ?? 0 }}</td>
                <td>{{ $user->orders_count ?? 0 }}</td>
                <td>{{ $user->created_at }}</td>
                <td>
                    <a href="{{ url('admin/users/'.$user->id.'/edit') }}" class="btn btn-sm">编辑</a>
                    <form method="POST" action="{{ url('admin/users/'.$user->id.'/toggle-status') }}" style="display:inline">
                        @csrf
                        <button class="btn btn-sm {{ $user->status?'btn-danger':'btn-primary' }}">{{ $user->status?'禁用':'启用' }}</button>
                    </form>
                    <form method="POST" action="{{ url('admin/users/'.$user->id.'/delete') }}" style="display:inline" onsubmit="return confirm('确定要删除用户 {{ $user->name }} 吗？\n\n如有卖书/订单/求购记录则无法删除，请先处理相关数据。')">
                        @csrf
                        <button class="btn btn-sm btn-danger">删除</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:40px;color:#999">暂无用户</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination">{{ $users->appends(request()->query())->links() }}</div>
</div>
@endsection
