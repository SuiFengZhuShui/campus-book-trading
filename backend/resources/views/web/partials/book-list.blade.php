@if($books->count() > 0)
    <div class="book-grid">
        @foreach($books as $book)
            <div class="book-card" onclick="location.href='/books/{{ $book->id }}'">
                @php $cover = $book->images->where('type', 'cover')->first(); @endphp
                <a href="/books/{{ $book->id }}" onclick="event.stopPropagation()" style="display: block;">
                @if($cover)
                    <img class="book-cover" src="{{ asset('storage/' . $cover->path) }}" alt="{{ $book->title }}">
                @else
                    <div class="book-cover">📖</div>
                @endif
                </a>
                <div class="book-info">
                    <a href="/books/{{ $book->id }}" class="book-title" style="display: block;">{{ $book->title }}</a>
                    <div class="book-meta">
                        <span>{{ $book->author }}</span>
                        <span class="condition-tag">{{ ['like_new'=>'全新','excellent'=>'几乎全新','good'=>'正常使用','fair'=>'较旧'][$book->condition] ?? $book->condition }}</span>
                    </div>
                    <div class="book-price">
                        <span class="book-price-current">{{ $book->price }}</span>
                        @if($book->original_price)
                            <span class="book-price-original">¥{{ $book->original_price }}</span>
                        @endif
                    </div>
                    @if(!auth()->check() || auth()->id() !== $book->seller_id)
                    <div style="display:flex;gap:6px;margin-top:10px;">
                        <a href="/buy/{{ $book->id }}" onclick="event.stopPropagation()" style="flex:1;padding:8px 0;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:20px;font-size:12px;text-align:center;font-weight:600;text-decoration:none;">立即购买</a>
                        <div style="flex:1;"><button onclick="event.stopPropagation();addToCart({{ $book->id }}, this)" style="width:100%;padding:8px 0;background:#ffffff;color:#b49450;border:1px solid #b49450;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;">加购物车</button></div>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="pagination">
        {{ $books->appends(request()->query())->links() }}
    </div>
@else
    <div style="text-align:center;padding:40px 20px;color:#6e6559;">
        <div style="font-size:64px;margin-bottom:12px;">📚</div>
        <p style="font-size:16px;">暂无在售书籍</p>
        <p style="margin-top:8px;">成为第一个卖书的同学吧！</p>
    </div>
@endif
