@extends('web.layouts.app')

@section('title', $want->title . ' - 求购详情 - 校园二手书网')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <a href="/wants" style="display: inline-flex; align-items: center; gap: 4px; padding: 8px 18px; background: linear-gradient(135deg, #b49450, #d4bc7c); color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; margin-bottom: 20px;">&larr; 返回求购广场</a>

    <div style="background: #fffdfa; border-radius: 12px; padding: 28px; border: 1px solid rgba(26,31,43,0.05); box-shadow: 0 2px 8px rgba(26,31,43,0.04);">
        <h2 style="font-size: 20px; font-weight: 600; color: #2c2416; margin-bottom: 20px;">{{ $want->title }}</h2>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 14px;">
            <div><span style="color: #8c8478;">学院：</span>{{ $want->category->name ?? '未指定' }}</div>
            <div><span style="color: #8c8478;">作者：</span>{{ $want->author }}</div>
            <div><span style="color: #8c8478;">出版社：</span>{{ $want->publisher }}</div>
            <div><span style="color: #8c8478;">最高接受价：</span><span style="font-size: 18px; font-weight: 700; color: #b49450;">¥{{ $want->max_price }}</span></div>
            <div><span style="color: #8c8478;">最低成色：</span>{{ implode('、', array_map(function($v) { return ['like_new'=>'全新','excellent'=>'几乎全新','good'=>'正常使用','fair'=>'较旧'][trim($v)] ?? trim($v); }, explode(',', $want->acceptable_condition))) }}</div>
            <div><span style="color: #8c8478;">发布者：</span>{{ $want->user->name ?? '匿名' }}</div>
            <div><span style="color: #8c8478;">有效期至：</span>{{ $want->expires_at->format('Y-m-d') }}</div>
        </div>

        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5dccf;">
            <a href="/sell?title={{ urlencode($want->title) }}&author={{ urlencode($want->author) }}&publisher={{ urlencode($want->publisher) }}&category_id={{ $want->category_id }}" class="btn-amber" style="width: 100%; text-align: center; text-decoration: none; display: block;">我要卖这本书</a>
            <p style="font-size: 12px; color: #8c8478; text-align: center; margin-top: 8px;">点击后将跳转到卖书页面，信息已自动填好</p>
        </div>
    </div>
</div>
@endsection
