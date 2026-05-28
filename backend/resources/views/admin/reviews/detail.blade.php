@extends('admin.layouts.admin')

@section('content')
<div class="breadcrumb"><a href="{{ url('admin') }}">首页</a> / <a href="{{ url('admin/reviews') }}">审核管理</a> / 审核详情</div>
<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
    <a href="{{ url('admin/reviews') }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">&larr; 返回审核管理</a>
    <span class="page-title" style="margin:0;">审核详情 #{{ $book->id }}</span>
</div>

<div class="flex-row">
    <div class="flex-1" style="width:60%">
        <div class="card">
            <div class="card-title">图片展示</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                @foreach($book->images as $img)
                    <img src="{{ asset('storage/'.$img->path) }}" style="width:160px;height:200px;object-fit:cover;border-radius:4px;cursor:pointer" onclick="window.open(this.src)">
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-title">书籍信息</div>
            <form id="approve-form" method="POST" action="{{ url('admin/reviews/'.$book->id.'/approve') }}">
                @csrf
                <div class="form-group"><label>书名</label><input type="text" name="title" class="form-control" value="{{ old('title', $book->title) }}"></div>
                <div class="flex-row">
                    <div class="flex-1 form-group"><label>作者</label><input type="text" name="author" class="form-control" value="{{ old('author', $book->author) }}"></div>
                    <div class="flex-1 form-group"><label>出版社</label><input type="text" name="publisher" class="form-control" value="{{ old('publisher', $book->publisher) }}"></div>
                </div>
                <div class="flex-row">
                    <div class="flex-1 form-group"><label>ISBN</label><input type="text" name="isbn" class="form-control" value="{{ old('isbn', $book->isbn) }}"></div>
                    <div class="flex-1 form-group">
                        <label>分类</label>
                        <select name="category_id" class="form-control">
                            @foreach(\App\Category::orderBy('sort')->get() as $cat)
                                <option value="{{ $cat->id }}" {{ ($book->category_id == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex-row">
                    <div class="flex-1 form-group">
                        <label>成色</label>
                        <select name="condition" class="form-control">
                            <option value="like_new" {{ $book->condition=='like_new'?'selected':'' }}>全新</option>
                            <option value="excellent" {{ $book->condition=='excellent'?'selected':'' }}>几乎全新</option>
                            <option value="good" {{ $book->condition=='good'?'selected':'' }}>正常使用</option>
                            <option value="fair" {{ $book->condition=='fair'?'selected':'' }}>较旧</option>
                        </select>
                    </div>
                    <div class="flex-1 form-group"><label>原价</label><input type="number" step="0.01" name="original_price" class="form-control" value="{{ old('original_price', $book->original_price) }}"></div>
                </div>
            </form>
        </div>
    </div>

    <div style="width:40%">
        <div class="card">
            <div class="card-title">学生信息</div>
            <table>
                <tr><td width="80">姓名</td><td>{{ $book->seller->name ?? '-' }}</td></tr>
                <tr><td>学号</td><td>{{ $book->seller->student_id ?? '-' }}</td></tr>
                <tr><td>手机号</td><td>{{ $book->seller->phone ?? '-' }}</td></tr>
                <tr><td>提交时间</td><td>{{ $book->submitted_at }}</td></tr>
            </table>
        </div>

        @if($book->status === 'pending_review')
        <div class="card">
            <div class="card-title">定价</div>
            <div class="form-group"><label>建议售价</label><input type="text" class="form-control" value="¥{{ $suggestPrice }}" readonly style="background:#fefdfb;color:#8c8478"></div>
            <div class="form-group"><label>最终售价</label><input type="number" step="0.01" name="price" form="approve-form" class="form-control" value="{{ old('price', $book->price ?: $suggestPrice) }}" required></div>
            <div class="form-group"><label>收书价</label><input type="number" step="0.01" name="cost_price" form="approve-form" class="form-control" value="{{ old('cost_price', $book->cost_price ?: round($suggestPrice * 0.5, 2)) }}" required></div>
            <p class="text-muted">收书价建议为售价的 40%-60%</p>
        </div>

        <div class="card">
            <button type="submit" form="approve-form" class="btn btn-primary" style="width:100%;margin-bottom:8px;padding:10px">审核通过</button>
            <button class="btn btn-danger" style="width:100%;padding:10px" onclick="reject()">驳回</button>
        </div>

        <form id="reject-form" method="POST" action="{{ url('admin/reviews/'.$book->id.'/reject') }}" style="display:none">
            @csrf
            <input type="hidden" name="reason" id="reject-reason">
        </form>
        <script>
        function reject() {
            var reason = prompt('请输入驳回原因（必填，最多255字）：');
            if (!reason) return;
            document.getElementById('reject-reason').value = reason;
            document.getElementById('reject-form').submit();
        }
        </script>
        @else
        <div class="card">
            <table>
                <tr><td>状态</td><td><span class="badge badge-{{ $book->status=='approved'?'green':($book->status=='removed'?'red':'gray') }}">{{ ['pending_review'=>'待审核','approved'=>'已通过','active'=>'在售','sold'=>'已售出','removed'=>'已驳回/下架'][$book->status] ?? $book->status }}</span></td></tr>
                <tr><td>售价</td><td>¥{{ $book->price }}</td></tr>
                <tr><td>收书价</td><td>¥{{ $book->cost_price }}</td></tr>
                @if($book->status === 'removed' && $book->reject_reason)
                <tr><td>驳回原因</td><td style="color:#bc4742">{{ $book->reject_reason }}</td></tr>
                @endif
                @if($book->status === 'approved')
                <tr><td colspan="2"><button class="btn btn-primary btn-sm" onclick="receiveBook({{ $book->id }})" style="margin-top:8px">确认入库</button></td></tr>
                @endif
            </table>
        </div>
        @endif
    </div>
</div>

@if($book->status === 'approved')
<script>
function receiveBook(id) {
    var price = prompt('售价（¥），回车保持当前：', {{ $book->price }});
    if (price === null) return;
    var cost = prompt('收书价（¥），回车保持当前：', {{ $book->cost_price }});
    if (cost === null) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/reviews/' + id + '/receive';
    form.innerHTML = '@csrf<input name="price" value="' + price + '"><input name="cost_price" value="' + cost + '">';
    document.body.appendChild(form);
    form.submit();
}
</script>
@endif
@endsection
