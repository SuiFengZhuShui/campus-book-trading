@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / 院校管理</div>
<div class="page-title">院校管理</div>

<div class="card">
    <form method="POST" action="{{ url('admin/colleges') }}" class="flex-row" style="margin-bottom:16px">
        @csrf
        <input type="text" name="name" class="form-control" placeholder="新学院名称" style="width:220px" required>
        <button type="submit" class="btn btn-primary">+ 新增学院</button>
    </form>

    <table>
        <thead>
            <tr><th style="width:60px">排序</th><th>学院名称</th><th style="width:140px">操作</th></tr>
        </thead>
        <tbody>
            @forelse($colleges as $college)
            <tr>
                <td>{{ $college->sort }}</td>
                <td>
                    <span id="name-{{ $college->id }}">{{ $college->name }}</span>
                    <form id="edit-{{ $college->id }}" method="POST" action="{{ url('admin/colleges/'.$college->id) }}" style="display:none" class="flex-row">
                        @csrf
                        <input type="text" name="name" class="form-control" value="{{ $college->name }}" style="width:200px" required>
                        <button type="submit" class="btn btn-primary btn-sm">保存</button>
                        <button type="button" class="btn btn-sm" onclick="cancelEdit({{ $college->id }})">取消</button>
                    </form>
                </td>
                <td>
                    <button class="btn btn-sm" onclick="editCollege({{ $college->id }})">编辑</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteCollege({{ $college->id }}, '{{ $college->name }}')">删除</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="empty-state">暂无学院数据</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function editCollege(id) {
    document.getElementById('name-' + id).style.display = 'none';
    document.getElementById('edit-' + id).style.display = 'flex';
    document.getElementById('edit-' + id).querySelector('input').focus();
}
function cancelEdit(id) {
    document.getElementById('name-' + id).style.display = 'inline';
    document.getElementById('edit-' + id).style.display = 'none';
}
function deleteCollege(id, name) {
    if (!confirm('确认删除「' + name + '」？')) return;
    var f = document.createElement('form');
    f.method = 'POST'; f.action = '/admin/colleges/' + id + '/delete';
    f.innerHTML = '@csrf'; document.body.appendChild(f); f.submit();
}
</script>
@endsection
