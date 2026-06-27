@extends('web.layouts.app')

@section('title', $book->title . ' - 校园二手书网')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">
    {{-- 返回按钮 — history.back() 回到来源页，无历史则回首页 --}}
    <div style="margin-bottom: 18px;">
        @php
            $backUrl = '/';
            $ref = request()->server('HTTP_REFERER', '');
            if (strpos($ref, '/my-sells') !== false) { $backUrl = '/my-sells'; }
            elseif (strpos($ref, '/orders') !== false) { $backUrl = '/orders'; }
        @endphp
        <a href="{{ $backUrl }}" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#c7915c,#d4a574);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">&larr; 返回</a>
    </div>

    {{-- 面包屑 --}}
    <div style="margin-bottom: 20px; font-size: 13px; color: #6e6559;">

    {{-- 驳回提示 --}}
    @if(in_array($book->status, ['removed', 'rejected']) && $book->reject_reason)
        <div style="background: linear-gradient(135deg, #6b2737, #8b3a4a); color: #fff; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
            <strong>⚠ 此书籍已被驳回</strong><br>
            驳回原因：{{ $book->reject_reason }}
        </div>
    @endif
        <a href="/" style="color: #b49450;">首页</a>
        <span style="margin: 0 6px;">›</span>
        <a href="/?category_id={{ $book->category_id }}" style="color: #b49450;">{{ $book->category->name ?? '全部' }}</a>
        <span style="margin: 0 6px;">›</span>
        <span style="color: #2c2416;">{{ $book->title }}</span>
    </div>

    <div style="display: flex; gap: 36px; flex-wrap: wrap;">
        {{-- 左侧图片区 --}}
        <div style="flex: 0 0 400px; max-width: 400px;">
            @php $allImages = $book->images->sortBy('sort')->values(); @endphp
            @php $imgCount = $allImages->count(); @endphp
            <div style="position: relative; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 16px rgba(26,31,43,0.1), 0 1px 3px rgba(0,0,0,0.06); border: 1px solid rgba(26,31,43,0.06); background: #f4f1ec;">
                @if($imgCount > 0)
                    <img id="main-img" src="{{ asset('storage/' . $allImages->first()->path) }}" alt="{{ $book->title }}"
                         style="width: 100%; height: 560px; object-fit: cover; display: block;">
                    @if($imgCount > 1)
                    <button onclick="prevImage()" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(44,36,22,0.6); color: #fff; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(44,36,22,0.85)'" onmouseout="this.style.background='rgba(44,36,22,0.6)'">◀</button>
                    <button onclick="nextImage()" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; border-radius: 50%; border: none; background: rgba(44,36,22,0.6); color: #fff; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='rgba(44,36,22,0.85)'" onmouseout="this.style.background='rgba(44,36,22,0.6)'">▶</button>
                    <div id="img-counter" style="position: absolute; bottom: 12px; right: 12px; background: rgba(44,36,22,0.6); color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 12px;">1 / {{ $imgCount }}</div>
                    @endif
                @else
                    <div style="width: 100%; height: 560px; display: flex; align-items: center; justify-content: center; font-size: 120px; color: #8a8070;">📖</div>
                @endif
            </div>

            {{-- 缩略图轮播 --}}
            @if($allImages->count() > 1)
                <div style="display: flex; gap: 8px; margin-top: 12px; overflow-x: auto;">
                    @foreach($allImages as $idx => $img)
                        <div style="flex-shrink: 0; width: 90px; height: 90px; border-radius: 10px; overflow: hidden; cursor: pointer; border: 2px solid {{ $idx === 0 ? '#b49450' : 'transparent' }}; box-shadow: 0 1px 3px rgba(26,31,43,0.06); transition: border-color 0.15s;"
                             id="thumb-{{ $idx }}"
                             onclick="switchImage('{{ asset('storage/' . $img->path) }}', {{ $idx }})">
                            <img src="{{ asset('storage/' . $img->path) }}" alt="图片{{ $idx + 1 }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 右侧信息区 --}}
        <div style="flex: 1; min-width: 300px;">
            <h1 style="font-size: 24px; font-weight: 700; color: #2c2416; line-height: 1.4; margin-bottom: 12px; letter-spacing: -0.01em;">{{ $book->title }}</h1>

            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
                <span style="display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 500; background: rgba(180,148,80,0.08); color: #b49450;">{{ $book->category->name ?? '未分类' }}</span>
                @php
                    $condMap = ['like_new'=>'全新', 'excellent'=>'几乎全新', 'good'=>'正常使用', 'fair'=>'较旧'];
                    $condBg = ['like_new'=>'#edf5f0','excellent'=>'#edf5f0','good'=>'#fef9f0','fair'=>'#fdf2f1'];
                    $condColor = ['like_new'=>'#2d6a4f','excellent'=>'#2d6a4f','good'=>'#b0822c','fair'=>'#bc4742'];
                @endphp
                <span style="display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 500; background: {{ $condBg[$book->condition] ?? '#f4f1ec' }}; color: {{ $condColor[$book->condition] ?? '#6e6559' }};">{{ $condMap[$book->condition] ?? $book->condition }}</span>
            </div>

            {{-- 价格 --}}
            <div style="background: linear-gradient(135deg, #fef9f0, #fdf5e6); border-radius: 12px; padding: 24px; margin-bottom: 20px; border-left: 4px solid #b49450;">
                <div style="display: flex; align-items: baseline; gap: 16px;">
                    <div>
                        <div style="font-size: 12px; color: #6e6559; margin-bottom: 4px;">平台售价</div>
                        @if($book->price)
                        <span style="font-size: 36px; color: #b49450; font-weight: 400; font-family: 'LXGW WenKai', serif;">¥{{ $book->price }}</span>
                        @elseif($book->status === 'rejected')
                        <span style="font-size: 18px; color: #bc4742; font-weight: 500;">已被驳回</span>
                        @else
                        <span style="font-size: 18px; color: #b0822c; font-weight: 500;">审核中，待定价</span>
                        @endif
                    </div>
                    @if($book->original_price && $book->price)
                        <div>
                            <div style="font-size: 12px; color: #6e6559; margin-bottom: 4px;">原价</div>
                            <span style="font-size: 20px; color: #5a5145; text-decoration: line-through; font-family: 'LXGW WenKai', serif;">¥{{ $book->original_price }}</span>
                        </div>
                        <div style="font-size: 13px; color: #2d6a4f; font-weight: 500; margin-left: auto;">
                            省 ¥{{ number_format($book->original_price - $book->price, 2) }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- 基本信息 --}}
            <div style="background: #ffffff; border-radius: 12px; padding: 20px 24px; border: 1px solid #cec4b0; box-shadow: 0 1px 2px rgba(0,0,0,0.04); margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 14px;">
                    <div>
                        <span style="color: #6e6559;">作者：</span>
                        <span style="color: #2c2416;">{{ $book->author }}</span>
                    </div>
                    <div>
                        <span style="color: #6e6559;">出版社：</span>
                        <span style="color: #2c2416;">{{ $book->publisher }}</span>
                    </div>
                    @if($book->isbn)
                        <div>
                            <span style="color: #6e6559;">ISBN：</span>
                            <span style="color: #2c2416;">{{ $book->isbn }}</span>
                        </div>
                    @endif
                    @if($book->course)
                        <div>
                            <span style="color: #6e6559;">适用课程：</span>
                            <span style="color: #2c2416;">{{ $book->course->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 购买按钮 --}}
            @if($book->status === 'active' && (!auth()->check() || auth()->id() !== $book->seller_id))
            <div style="display:flex;gap:12px;">
                <a href="/buy/{{ $book->id }}" class="btn-amber"
                        style="display:flex;align-items:center;justify-content:center;flex:2;height:52px;font-size:18px;">
                    立即购买
                </a>
                <div style="flex:1;">
                    <button onclick="addToCartDetail({{ $book->id }}, this)" style="width:100%;height:52px;background:#ffffff;color:#b49450;border:1px solid #b49450;border-radius:12px;font-size:15px;font-weight:500;cursor:pointer;">加入购物车</button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- 书籍描述 --}}
    @if($book->description)
        <div style="margin-top: 36px; background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #cec4b0; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
            <h3 style="font-size: 16px; font-weight: 600; color: #2c2416; margin-bottom: 4px;">补充说明</h3>
            <div style="width: 36px; height: 2px; background: #b49450; border-radius: 1px; margin-bottom: 16px;"></div>
            <p style="font-size: 14px; color: #6e6559; line-height: 1.8; white-space: pre-wrap;">{{ $book->description }}</p>
        </div>
    @endif

    {{-- 卖家信息 --}}
    @if(!auth()->check() || auth()->id() !== $book->seller_id)
    <div style="margin-top: 24px; background: #ffffff; border-radius: 12px; padding: 20px 24px; border: 1px solid #cec4b0; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #b49450, #d4bc7c); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px;">👤</div>
            <div>
                <div style="font-size: 14px; font-weight: 500; color: #2c2416;">卖家：{{ $book->seller->name ?? '未知' }}</div>
                <div style="font-size: 12px; color: #6e6559;">购买后可查看联系方式</div>
            </div>
        </div>
        @if($sellerReviews->isNotEmpty())
        <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid #e5dccf;">
            <div style="font-size: 14px; font-weight: 600; color: #2c2416; margin-bottom: 10px;">历史评价</div>
            @foreach($sellerReviews as $r)
            <div style="padding: 8px 0; {{ !$loop->last ? 'border-bottom: 1px solid #e5dccf;' : '' }}">
                <div style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
                    <span style="color: #b49450; letter-spacing: 1px;">{{ str_repeat('★', $r->book_rating) }}{{ str_repeat('☆', 5 - $r->book_rating) }}</span>
                    <span style="color: #6e6559;">书况</span>
                    <span style="color: #b49450; letter-spacing: 1px; margin-left: 6px;">{{ str_repeat('★', $r->service_rating) }}{{ str_repeat('☆', 5 - $r->service_rating) }}</span>
                    <span style="color: #6e6559;">服务</span>
                    <span style="margin-left: auto; font-size: 12px; color: #8c8478;">{{ $r->user->name ?? '匿名' }}</span>
                </div>
                @if($r->comment)
                <div style="font-size: 13px; color: #2c2416; margin-top: 4px;">{{ $r->comment }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    @auth
    @if(auth()->id() === $book->seller_id && in_array($book->status, ['rejected', 'removed']))
    <div style="margin-top: 16px; text-align: right;">
        <button onclick="deleteBook({{ $book->id }}, '{{ $book->status }}')" style="padding:10px 24px;background:#bc4742;color:#fff;border:none;border-radius:8px;font-size:14px;cursor:pointer;">删除此书</button>
    </div>
    @endif
    @endauth
</div>
@endsection

@section('scripts')
<script>
var galleryImages = [
@foreach($allImages as $img)
    '{{ asset('storage/' . $img->path) }}'{{ $loop->last ? '' : ',' }}
@endforeach
];
var currentIdx = 0;
var totalImages = galleryImages.length;

function switchImage(src, idx) {
    currentIdx = idx;
    document.getElementById('main-img').src = src;
    var counter = document.getElementById('img-counter');
    if (counter) counter.textContent = (idx + 1) + ' / ' + totalImages;
    document.querySelectorAll('[id^="thumb-"]').forEach(function(el) { el.style.borderColor = 'transparent'; });
    var thumb = document.getElementById('thumb-' + idx);
    if (thumb) thumb.style.borderColor = '#b49450';
}

function prevImage() {
    var newIdx = (currentIdx - 1 + totalImages) % totalImages;
    switchImage(galleryImages[newIdx], newIdx);
}

function nextImage() {
    var newIdx = (currentIdx + 1) % totalImages;
    switchImage(galleryImages[newIdx], newIdx);
}

function deleteBook(bookId, status) {
    var msg = status === 'rejected' ? '确定删除此被驳回的书吗？' : '确定删除此被下架的书吗？';
    if (!confirm(msg)) return;
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/my-sells/' + bookId + '/delete', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' }
    }).then(function(r) {
        if (r.ok) { location.href = '/my-sells'; }
        else { r.json().then(function(d) { alert(d.message || '删除失败'); }); }
    }).catch(function() { alert('网络错误'); });
}

function addToCartDetail(bookId, btn) {
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    btn.disabled = true;
    btn.textContent = '...';
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ book_id: bookId })
    }).then(function(r) { return r.json() }).then(function(d) {
        if (d.code === 200) {
            btn.textContent = '已加入 ✓';
            btn.style.color = '#4a6741';
            btn.style.borderColor = '#4a6741';
            var badge = document.getElementById('cart-badge');
            var cur = badge ? parseInt(badge.textContent) || 0 : 0;
            if (typeof updateCartBadge === 'function') updateCartBadge(cur + 1);
        } else if (d.code === 401) {
            location.href = '/login';
        } else {
            alert(d.message || '操作失败');
            btn.disabled = false;
            btn.textContent = '加入购物车';
        }
    }).catch(function() {
        btn.disabled = false;
        btn.textContent = '加入购物车';
    });
}
</script>
@endsection
