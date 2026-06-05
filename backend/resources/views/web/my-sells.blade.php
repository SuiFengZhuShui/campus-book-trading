@extends('web.layouts.app')

@section('title', '我的卖书 - 校园二手书网')

@section('content')
<h2 style="font-size: 22px; font-weight: 600; color: #2c2416; letter-spacing: -0.01em; margin-bottom: 20px;">我卖的书</h2>

@if($books->count() > 0)
    <div style="display: flex; flex-direction: column; gap: 12px;">
        @foreach($books as $book)
            @php
                $statusBorder = ['pending_review'=>'#b0822c','approved'=>'#b49450','active'=>'#2d6a4f','sold'=>'#6e6559','rejected'=>'#bc4742','removed'=>'#8a8070'];
                $statusMap = ['pending_review'=>'待审核','approved'=>'已通过','active'=>'在售','sold'=>'已售出','rejected'=>'已驳回','removed'=>'已下架'];
                $statusBg = ['pending_review'=>'#fef9f0','approved'=>'rgba(180,148,80,0.08)','active'=>'#edf5f0','sold'=>'#f4f1ec','rejected'=>'#fdf2f1','removed'=>'#f4f1ec'];
                $statusColor = ['pending_review'=>'#b0822c','approved'=>'#b49450','active'=>'#2d6a4f','sold'=>'#6e6559','rejected'=>'#bc4742','removed'=>'#6e6559'];
            @endphp
            <div style="background: #ffffff; border-radius: 12px; padding: 16px 20px; border: 1px solid #cec4b0; box-shadow: 0 1px 2px rgba(0,0,0,0.04); display: flex; gap: 16px; align-items: center; border-left: 4px solid {{ $statusBorder[$book->status] ?? '#8a8070' }}; transition: box-shadow 0.25s;">
                @php $cover = $book->images->where('type', 'cover')->first(); @endphp
                @if($cover)
                    <img src="{{ asset('storage/' . $cover->path) }}" alt="{{ $book->title }}"
                         style="width: 56px; height: 78px; object-fit: cover; border-radius: 6px; flex-shrink: 0;">
                @else
                    <div style="width: 56px; height: 78px; border-radius: 6px; background: #f4f1ec; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">📖</div>
                @endif
                <div style="flex: 1; min-width: 0;">
                    <a href="/books/{{ $book->id }}" style="font-size: 14px; font-weight: 500; color: #2c2416;">{{ $book->title }}</a>
                    <div style="font-size: 12px; color: #6e6559; margin-top: 2px;">{{ $book->author }} / {{ $book->publisher }}</div>
                            @if($book->reject_reason)
                        <div style="font-size: 12px; color: #bc4742; margin-top: 2px;">驳回原因：{{ $book->reject_reason }}</div>
                    @endif
                </div>
                <div style="text-align: right; flex-shrink: 0;">
                    <span style="display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; letter-spacing: 0.03em; background: {{ $statusBg[$book->status] ?? '#f4f1ec' }}; color: {{ $statusColor[$book->status] ?? '#6e6559' }};">
                        {{ $statusMap[$book->status] ?? $book->status }}
                    </span>
                    @if(in_array($book->status, ['removed', 'rejected']))
                        <form class="delete-book-form" data-id="{{ $book->id }}" data-status="{{ $book->status }}" style="margin-top: 6px;">
                            @csrf
                            <button type="button" class="btn-delete-book" style="font-size: 12px; color: #bc4742; background: none; border: 1px solid #f5c6cb; border-radius: 6px; padding: 3px 10px; cursor: pointer;">删除</button>
                        </form>
                    @endif
                    <div style="font-size: 16px; font-weight: 600; color: #b49450; margin-top: 4px;">¥{{ $book->price }}</div>
                    @if($book->status === 'sold' && $book->orderItems->isNotEmpty())
                        @php $orderId = $book->orderItems->first()->order_id; @endphp
                        <a href="/orders/{{ $orderId }}?from=sells" style="display:inline-block;margin-top:4px;font-size:12px;color:#b49450;">查看订单</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="pagination">
        {{ $books->links() }}
    </div>
@else
    <div class="no-books">
        <div class="icon">📦</div>
        <p style="font-size: 16px;">还没有卖过书</p>
        <p style="margin-top: 8px;">去<a href="/sell" style="color: #b49450;">卖书</a>赚点零花钱吧</p>
    </div>
@endif
<script>
document.querySelectorAll('.btn-delete-book').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var form = this.closest('form');
        var status = form.dataset.status;
        var msg = status === 'rejected' ? '确定删除被驳回的书吗？' : '确定删除被下架的书吗？';
        if (!confirm(msg)) return;
        var token = form.querySelector('input[name="_token"]').value;
        var id = form.dataset.id;
        fetch('/my-sells/' + id + '/delete', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' },
            body: JSON.stringify({ _token: token })
        }).then(function(r) {
            if (r.ok) { location.reload(); }
            else { r.json().then(function(d) { alert(d.message || '删除失败'); }); }
        }).catch(function() { alert('网络错误'); });
    });
});
</script>
@endsection
