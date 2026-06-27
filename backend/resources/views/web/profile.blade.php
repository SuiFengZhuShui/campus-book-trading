@extends('web.layouts.app')

@section('title', '个人中心 - 校园二手书网')

@section('content')
<a href="/" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:24px;">&larr; 返回</a>

{{-- 用户横幅 --}}
<div style="background: linear-gradient(160deg, #2c2416 0%, #3d3020 40%, #b49450 100%); border-radius: 16px; padding: 48px 48px 36px; margin-bottom: 28px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
    <div style="display: flex; align-items: center; gap: 28px;">
        <div style="width: 88px; height: 88px; border-radius: 50%; background: rgba(255,255,255,0.15); border: 3px solid rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <span style="font-size: 38px; color: #fff; font-weight: 700;">{{ mb_substr($user->name, 0, 1) }}</span>
        </div>
        <div style="flex: 1;">
            <div style="font-size: 28px; color: #fff; font-weight: 700;">{{ $user->name }}</div>
            <div style="font-size: 14px; color: rgba(255,255,255,0.6); margin-top: 6px;">学号 {{ $user->student_id ?: '未设置' }}　|　{{ substr($user->phone, 0, 3) . '****' . substr($user->phone, -4) }}</div>
        </div>
        <div style="display: flex; align-items: center; gap: 40px;">
            <a href="/profile/edit" style="padding:10px 22px;border:1px solid rgba(255,255,255,0.4);border-radius:10px;color:#fff;font-size:13px;text-decoration:none;white-space:nowrap;">修改信息</a>
            <div style="text-align: center;">
                <div style="font-size: 32px; font-weight: 400; color: #fff; font-family: 'LXGW WenKai', serif;">{{ $orderCount }}</div>
                <div style="font-size: 13px; color: rgba(255,255,255,0.5); margin-top: 4px;">我的订单</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 32px; font-weight: 400; color: #fff; font-family: 'LXGW WenKai', serif;">{{ $sellCount }}</div>
                <div style="font-size: 13px; color: rgba(255,255,255,0.5); margin-top: 4px;">在售书籍</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #fff;">{{ $cartCount }}</div>
                <div style="font-size: 13px; color: rgba(255,255,255,0.5); margin-top: 4px;">购物车</div>
            </div>
        </div>
    </div>
</div>

{{-- 订单状态 + 菜单 --}}
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 28px;">

    {{-- 左：订单状态 --}}
    <div>
        <div style="font-size: 14px; color: #2c2416; font-weight: 600; margin-bottom: 16px;">订单状态</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            @php
                $statItems = [
                    ['label' => '待付款', 'count' => $orderStats['pending'], 'status' => 'pending', 'color' => '#b0822c', 'bg' => '#fdf3e0'],
                    ['label' => '已付款', 'count' => $orderStats['paid'], 'status' => 'paid', 'color' => '#b49450', 'bg' => '#fdf6ec'],
                    ['label' => '待取书', 'count' => $orderStats['confirmed'], 'status' => 'confirmed', 'color' => '#5b7fbd', 'bg' => '#eef2f8'],
                    ['label' => '已完成', 'count' => $orderStats['picked_up'], 'status' => 'picked_up', 'color' => '#2d6a4f', 'bg' => '#e8f0e9'],
                ];
            @endphp
            @foreach($statItems as $item)
                <a href="/orders?status={{ $item['status'] }}&from=profile" style="background: {{ $item['bg'] }}; border-radius: 12px; padding: 24px 20px; text-decoration: none; display: block; border: 1px solid #cec4b0; transition: transform 0.2s;">
                    <div style="font-size: 13px; color: #8c8478; margin-bottom: 6px;">{{ $item['label'] }}</div>
                    <div style="font-size: 36px; font-weight: 700; color: {{ $item['color'] }};">{{ $item['count'] }}</div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- 右：功能菜单 --}}
    <div>
        <div style="font-size: 14px; color: #2c2416; font-weight: 600; margin-bottom: 16px;">功能菜单</div>
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid #cec4b0; box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
            <a href="/orders?from=profile" style="display: flex; align-items: center; padding: 16px 24px; font-size: 15px; color: #2c2416; text-decoration: none; border-bottom: 1px solid #e5dccf;">
                <span style="font-size: 20px; margin-right: 14px;">📋</span> 我的订单 <span style="margin-left: auto; color: #d0cdc8; font-size: 20px;">›</span>
            </a>
            <a href="/my-sells" style="display: flex; align-items: center; padding: 16px 24px; font-size: 15px; color: #2c2416; text-decoration: none; border-bottom: 1px solid #e5dccf;">
                <span style="font-size: 20px; margin-right: 14px;">📖</span> 我的卖书 <span style="margin-left: auto; color: #d0cdc8; font-size: 20px;">›</span>
            </a>
            <a href="/cart" style="display: flex; align-items: center; padding: 16px 24px; font-size: 15px; color: #2c2416; text-decoration: none; border-bottom: 1px solid #e5dccf;">
                <span style="font-size: 20px; margin-right: 14px;">🛒</span> 购物车 <span style="margin-left: auto; color: #d0cdc8; font-size: 20px;">›</span>
            </a>
            <a href="/wants?mine=1" style="display: flex; align-items: center; padding: 16px 24px; font-size: 15px; color: #2c2416; text-decoration: none; border-bottom: 1px solid #e5dccf;">
                <span style="font-size: 20px; margin-right: 14px;">🔍</span> 我的求购 <span style="margin-left: auto; color: #d0cdc8; font-size: 20px;">›</span>
            </a>
            <a href="/sell" style="display: flex; align-items: center; padding: 16px 24px; font-size: 15px; color: #2c2416; text-decoration: none; border-bottom: 1px solid #e5dccf;">
                <span style="font-size: 20px; margin-right: 14px;">✏️</span> 我要卖书 <span style="margin-left: auto; color: #d0cdc8; font-size: 20px;">›</span>
            </a>
            <a href="/post-want" style="display: flex; align-items: center; padding: 16px 24px; font-size: 15px; color: #2c2416; text-decoration: none;">
                <span style="font-size: 20px; margin-right: 14px;">📝</span> 发布求购 <span style="margin-left: auto; color: #d0cdc8; font-size: 20px;">›</span>
            </a>
        </div>

        <form id="logout-form-profile" action="/logout" method="POST" style="display: none;">@csrf</form>
        <div onclick="document.getElementById('logout-form-profile').submit()" style="margin-top: 20px; text-align: center; padding: 14px; border: 1px solid #e5dccf; border-radius: 12px; color: #8c8478; font-size: 14px; background: #fff; cursor: pointer;">退出登录</div>
    </div>
</div>
@endsection
