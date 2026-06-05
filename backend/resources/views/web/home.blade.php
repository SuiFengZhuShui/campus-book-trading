@extends('web.layouts.app')

@section('title', '校园二手书网 - 二手教材交易平台')

@section('content')
    @if(!request('keyword') && !request('category_id'))
    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 40px; align-items: center; min-height: 400px; margin-bottom: 28px;">
        <div>
            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 48px; font-weight: 600; line-height: 1.15; letter-spacing: -0.015em; margin-bottom: 16px; color: #2c2416;">
                旧课本的<em style="font-style: italic; color: #b49450;">下一次</em>翻页
            </h2>
            <div style="width: 60px; height: 2px; background: linear-gradient(90deg, #b49450, transparent); margin-bottom: 20px;"></div>
            <p style="font-size: 17px; color: #6e6559; max-width: 440px; line-height: 1.9; margin-bottom: 28px;">
                知识的传递无需全新的纸张。在校园里，每一本被翻阅过的教材，都等待着下一双手的开启。
            </p>
            <div style="display: flex; gap: 14px;">
                <a href="/sell" style="display: inline-flex; align-items: center; gap: 6px; padding: 14px 32px; background: linear-gradient(135deg, #6b2737, #8b3a4a); color: #fff; border-radius: 24px; font-size: 15px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 16px rgba(107,39,55,0.2); transition: all 0.3s;">✦ 出售闲置教材</a>
                <a href="/wants" style="display: inline-flex; align-items: center; padding: 14px 28px; border: 1px solid #cec4b0; border-radius: 24px; color: #2c2416; font-size: 15px; text-decoration: none; transition: all 0.3s;">查看求购</a>
            </div>
        </div>
        <div style="background: #fffdf9; border: 1px solid #cec4b0; border-radius: 20px; padding: 36px 32px; position: relative; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.04);">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #b49450, #d4bc7c, #6b2737);"></div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div style="font-family: 'Cormorant Garamond', serif; font-size: 42px; font-weight: 700; color: #b49450; line-height: 1.1;">{{ $books->total() }}+</div>
                    <div style="font-size: 13px; color: #6e6559;">在售课本</div>
                </div>
                <div>
                    <div style="font-family: 'Cormorant Garamond', serif; font-size: 42px; font-weight: 700; color: #b49450; line-height: 1.1;">7</div>
                    <div style="font-size: 13px; color: #6e6559;">覆盖学院</div>
                </div>
                <div>
                    <div style="font-family: 'Cormorant Garamond', serif; font-size: 42px; font-weight: 700; color: #b49450; line-height: 1.1;">15+</div>
                    <div style="font-size: 13px; color: #6e6559;">活跃同学</div>
                </div>
                <div>
                    <div style="font-family: 'Cormorant Garamond', serif; font-size: 42px; font-weight: 700; color: #b49450; line-height: 1.1;">10+</div>
                    <div style="font-size: 13px; color: #6e6559;">成交订单</div>
                </div>
            </div>
            <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #cec4b0; font-style: italic; color: #6e6559; font-size: 15px; line-height: 1.8;">
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 36px; color: #b49450; line-height: 0; vertical-align: -6px; margin-right: 4px;">&ldquo;</span>书籍是横渡时间大海的航船，让它们在校园里继续航行。
            </div>
        </div>
    </div>
    <div style="background: linear-gradient(135deg, #2c2416, #3d3428); color: rgba(255,255,255,0.65); padding: 10px 0; margin-bottom: 28px; overflow: hidden; white-space: nowrap; border-top: 2px solid #b49450; border-bottom: 2px solid #b49450;">
        <marquee behavior="scroll" direction="left" scrollamount="4" style="font-size: 13px;">
            ✦ 只收教材课本，不收课外书 ✦ 覆盖全校七大学院 ✦ 同学直接交易，省心又省钱 ✦ 让旧课本在校园里流转 ✦
        </marquee>
    </div>
    @endif

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
        @if(request('keyword'))
            <p style="color: #6e6559; font-size: 14px; margin: 0;">
                搜索"<strong style="color: #2c2416;">{{ request('keyword') }}</strong>"的结果，共 {{ $books->total() }} 本书
            </p>
        @else
            <p style="color: #6e6559; font-size: 14px; margin: 0;">共 {{ $books->total() }} 本书</p>
        @endif
        <select style="height: 36px; padding: 0 32px 0 14px; border: 1px solid #cec4b0; border-radius: 20px; font-family: 'LXGW WenKai TC', serif; font-size: 13px; background: #fffdf9; color: #6e6559; cursor: pointer; -webkit-appearance: none; appearance: none; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23b49450'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 12px center;" onchange="location.href=this.value">
            <option value="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'newest', 'page' => null])) }}" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>最新上架</option>
            <option value="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'price_asc', 'page' => null])) }}" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>价格从低到高</option>
            <option value="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'price_desc', 'page' => null])) }}" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>价格从高到低</option>
            <option value="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'condition', 'page' => null])) }}" {{ request('sort') === 'condition' ? 'selected' : '' }}>成色最好优先</option>
        </select>
    </div>

    @if($books->count() > 0)
        <div class="book-grid">
            @foreach($books as $book)
                <div class="book-card" style="cursor: default;">
                    @php $cover = $book->images->where('type', 'cover')->first(); @endphp
                    <a href="/books/{{ $book->id }}" style="display: block;">
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
                        <a href="/buy/{{ $book->id }}" style="display: block; margin-top: 10px; padding: 8px 0; background: linear-gradient(135deg, #b49450, #d4bc7c); color: #fff; border: none; border-radius: 20px; font-size: 12px; text-align: center; font-weight: 600; text-decoration: none; transition: all 0.3s;">立即购买</a>
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
@endsection
