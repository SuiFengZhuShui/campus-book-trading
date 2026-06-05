@extends('web.layouts.app')

@section('title', $book->title . ' - 校园二手书网')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">
    {{-- 面包屑 --}}
    <div style="margin-bottom: 20px; font-size: 13px; color: #6e6559;">

    {{-- 驳回提示 --}}
    @if($book->status === 'removed' && $book->reject_reason)
        <div style="background: #fdf2f1; border: 1px solid #f5c6cb; color: #bc4742; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 14px;">
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
            @php $cover = $book->images->where('type', 'cover')->first(); @endphp
            <div style="border-radius: 14px; overflow: hidden; box-shadow: 0 4px 16px rgba(26,31,43,0.1), 0 1px 3px rgba(0,0,0,0.06); border: 1px solid rgba(26,31,43,0.06); background: #f4f1ec;">
                @if($cover)
                    <img id="cover-img" src="{{ asset('storage/' . $cover->path) }}" alt="{{ $book->title }}"
                         style="width: 100%; height: 560px; object-fit: cover; display: block;">
                @else
                    <div style="width: 100%; height: 560px; display: flex; align-items: center; justify-content: center; font-size: 120px; color: #8a8070;">📖</div>
                @endif
            </div>

            {{-- 实拍图 --}}
            @php $photos = $book->images->where('type', 'photo')->values(); @endphp
            @if($photos->isNotEmpty())
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 12px;">
                    @foreach($photos as $photo)
                        <div style="border-radius: 10px; overflow: hidden; aspect-ratio: 1; box-shadow: 0 1px 3px rgba(26,31,43,0.06); cursor: pointer; border: 2px solid transparent; transition: border-color 0.15s;"
                             onmouseover="this.style.borderColor='#b49450'"
                             onmouseout="this.style.borderColor='transparent'"
                             onclick="document.getElementById('cover-img').src='{{ asset('storage/' . $photo->path) }}'">
                            <img src="{{ asset('storage/' . $photo->path) }}" alt="实拍图"
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
                        <span style="font-size: 36px; color: #b49450; font-weight: 700;">¥{{ $book->price }}</span>
                        @else
                        <span style="font-size: 18px; color: #b0822c; font-weight: 500;">审核中，待定价</span>
                        @endif
                    </div>
                    @if($book->original_price && $book->price)
                        <div>
                            <div style="font-size: 12px; color: #6e6559; margin-bottom: 4px;">原价</div>
                            <span style="font-size: 20px; color: #5a5145; text-decoration: line-through;">¥{{ $book->original_price }}</span>
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
            <a href="/buy/{{ $book->id }}" class="btn-amber"
                    style="display: flex; align-items: center; justify-content: center; width: 100%; height: 52px; font-size: 18px;">
                立即购买
            </a>
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
    <div style="margin-top: 24px; background: #ffffff; border-radius: 12px; padding: 20px 24px; border: 1px solid #cec4b0; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #b49450, #d4bc7c); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px;">👤</div>
            <div>
                <div style="font-size: 14px; font-weight: 500; color: #2c2416;">卖家：{{ $book->seller->name ?? '未知' }}</div>
                <div style="font-size: 12px; color: #6e6559;">购买后可查看联系方式</div>
            </div>
        </div>
    </div>
</div>
@endsection
