@extends('web.layouts.app')

@section('title', '购物车 - 校园二手书网')

@section('content')
<a href="/" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:16px;">&larr; 返回首页</a>
<div style="max-width: 800px; margin: 0 auto;">
    <h2 style="font-size: 22px; font-weight: 600; color: #2c2416; margin-bottom: 20px;">🛒 我的购物车</h2>

    @if(session('success'))
        <div style="background:#edf5f0;color:#2d6a4f;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#fdf2f1;color:#bc4742;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;">{{ session('error') }}</div>
    @endif

    @if($items->count() > 0)
        <form id="cart-checkout" method="POST" action="/cart/checkout">
            @csrf
            <div style="background:#ffffff;border-radius:12px;border:1px solid #cec4b0;overflow:hidden;margin-bottom:16px;">
                @foreach($items as $item)
                    @php $book = $item->book; @endphp
                    @if($book)
                    <div style="display:flex;gap:14px;padding:14px 18px;align-items:center;{{ !$loop->last ? 'border-bottom:1px solid #cec4b0;' : '' }}">
                        <input type="checkbox" name="book_ids[]" value="{{ $book->id }}" class="cart-checkbox" checked
                               style="width:18px;height:18px;flex-shrink:0;accent-color:#b49450;">
                        @php $cover = $book->images->where('type', 'cover')->first(); @endphp
                        <a href="/books/{{ $book->id }}" style="flex-shrink:0;">
                            @if($cover)
                                <img src="{{ asset('storage/' . $cover->path) }}" alt="{{ $book->title }}"
                                     style="width:56px;height:78px;object-fit:cover;border-radius:6px;">
                            @else
                                <div style="width:56px;height:78px;border-radius:6px;background:#f4f1ec;display:flex;align-items:center;justify-content:center;font-size:24px;">📖</div>
                            @endif
                        </a>
                        <div style="flex:1;min-width:0;">
                            <a href="/books/{{ $book->id }}" style="font-size:14px;font-weight:500;color:#2c2416;line-height:1.3;">{{ $book->title }}</a>
                            <div style="font-size:12px;color:#6e6559;margin-top:2px;">{{ $book->author }} / {{ $book->publisher }}</div>
                            @if($book->status !== 'active')
                                <span style="font-size:11px;color:#bc4742;">（已下架/已售出）</span>
                            @endif
                        </div>
                        <div style="text-align:right;flex-shrink:0;">
                            <div style="font-size:16px;font-weight:700;color:#b49450;">¥{{ $book->price }}</div>
                            <a href="javascript:void(0)" onclick="removeItem({{ $item->id }})"
                               style="font-size:12px;color:#6e6559;margin-top:4px;display:inline-block;">移除</a>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            <div style="background:#ffffff;border-radius:12px;border:1px solid #cec4b0;padding:16px 18px;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                    <label style="font-size:14px;color:#2c2416;cursor:pointer;">
                        <input type="checkbox" id="select-all" checked style="width:16px;height:16px;accent-color:#b49450;margin-right:6px;">全选
                    </label>
                    <div style="display:flex;align-items:center;gap:16px;">
                        <span style="font-size:13px;color:#6e6559;">合计：</span>
                        <span id="total-amount" style="font-size:22px;font-weight:700;color:#b49450;">¥0</span>
                    </div>
                    <select name="pickup_location" required style="height:38px;border:1px solid #cec4b0;border-radius:8px;padding:0 10px;font-size:13px;color:#2c2416;">
                        <option value="">选择取书地点</option>
                        <option value="图书馆一楼大厅">图书馆一楼大厅</option>
                        <option value="第一食堂门口">第一食堂门口</option>
                        <option value="行政楼大厅">行政楼大厅</option>
                        <option value="教学楼A区">教学楼A区</option>
                        <option value="快递驿站">快递驿站</option>
                        <option value="其他">其他</option>
                    </select>
                    <button type="submit" class="btn-amber" style="padding:10px 28px;font-size:15px;border:none;border-radius:10px;cursor:pointer;"
                            onclick="return validateCheckout()">去结算</button>
                </div>
            </div>
        </form>

        <form id="remove-form" method="POST" style="display:none;">@csrf</form>
    @else
        <div style="text-align:center;padding:80px 20px;color:#6e6559;">
            <div style="font-size:64px;margin-bottom:16px;">🛒</div>
            <p style="font-size:16px;">购物车是空的</p>
            <a href="/" style="display:inline-block;margin-top:16px;padding:10px 28px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border-radius:24px;font-size:14px;text-decoration:none;">去逛逛</a>
        </div>
    @endif
</div>

<script>
function calcTotal() {
    var total = 0;
    document.querySelectorAll('.cart-checkbox:checked').forEach(function(cb) {
        var row = cb.closest('div[style]').parentNode;
        var priceEl = row.querySelector('div[style*="text-align:right"] div:first-child');
        if (priceEl) {
            total += parseFloat(priceEl.textContent.replace('¥', '')) || 0;
        }
    });
    document.getElementById('total-amount').textContent = '¥' + total.toFixed(2);
}

document.querySelectorAll('.cart-checkbox').forEach(function(cb) {
    cb.addEventListener('change', calcTotal);
});
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.cart-checkbox').forEach(function(cb) { cb.checked = this.checked; }.bind(this));
    calcTotal();
});
calcTotal();

function removeItem(id) {
    if (!confirm('确定移除此书籍？')) return;
    var form = document.getElementById('remove-form');
    form.action = '/cart/remove/' + id;
    form.submit();
}

function validateCheckout() {
    var checked = document.querySelectorAll('.cart-checkbox:checked');
    if (checked.length === 0) {
        alert('请至少选择一本教材');
        return false;
    }
    var loc = document.querySelector('[name="pickup_location"]');
    if (!loc.value) {
        alert('请选择取书地点');
        return false;
    }
    return true;
}
</script>
@endsection
