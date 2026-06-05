@extends('web.layouts.app')

@section('title', request('mine') ? '我的求购 - 校园二手书网' : '求购广场 - 校园二手书网')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 22px; font-weight: 600; color: #2c2416; letter-spacing: -0.01em; margin: 0;">{{ request('mine') ? '我的求购' : '求购广场' }}</h2>
    <div style="display:flex;gap:8px;">
        @auth
        @if(request('mine'))
        <a href="/wants" style="display:inline-flex;align-items:center;gap:4px;padding:10px 18px;border:1px solid #cec4b0;border-radius:8px;font-size:14px;color:#2c2416;text-decoration:none;">全部求购</a>
        @else
        <a href="/wants?mine=1" style="display:inline-flex;align-items:center;gap:4px;padding:10px 18px;background:linear-gradient(135deg,#4a6741,#5a7d51);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;">🔍 我的求购</a>
        @endif
        @endauth
        <a href="/post-want" style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;">发布求购</a>
    </div>
</div>

@if($wants->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        @foreach($wants as $want)
            <a href="/wants/{{ $want->id }}" style="display: block; background: #ffffff; border-radius: 12px; padding: 20px; border: 1px solid rgba(26,31,43,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: box-shadow 0.2s, border-color 0.2s;">
                <div style="font-size: 15px; font-weight: 600; color: #2c2416; line-height: 1.4; margin-bottom: 8px;">{{ $want->title }}</div>
                <div style="font-size: 13px; color: #6e6559; margin-bottom: 4px;">{{ $want->author }} / {{ $want->publisher }}</div>
                <div style="font-size: 12px; color: #b49450; margin-bottom: 12px;">{{ $want->category->name ?? '' }}</div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 18px; font-weight: 700; color: #b49450;">最高 ¥{{ $want->max_price }}</span>
                    <span style="font-size: 12px; color: #6e6559;">{{ $want->user->name ?? '匿名' }}</span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="pagination">
        {{ $wants->links() }}
    </div>
@else
    <div style="text-align: center; padding: 80px 20px; color: #6e6559;">
        <div style="font-size: 64px; margin-bottom: 16px;">🔍</div>
        <p style="font-size: 16px;">暂无求购信息</p>
        <p style="margin-top: 8px;">去<a href="/login" style="color: #b49450;">发布</a>第一条求购吧</p>
    </div>
@endif
@endsection
