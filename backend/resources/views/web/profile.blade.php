@extends('web.layouts.app')

@section('title', '个人中心 - 校园二手书网')

@section('content')
<a href="/" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:16px;">&larr; 返回首页</a>
<div style="max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #2c2416, #3d3020); border-radius: 14px; padding: 32px 28px; color: #fff; margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 18px; margin-bottom: 20px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #b49450, #d4bc7c); display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0;">👤</div>
            <div>
                <div style="font-size: 22px; font-weight: 700;">{{ $user->name }}</div>
                <div style="font-size: 13px; color: rgba(255,255,255,0.6); margin-top: 2px;">{{ $user->student_id ? '学号 ' . $user->student_id : '' }}</div>
                <div style="font-size: 13px; color: rgba(255,255,255,0.6);">{{ $user->phone }}</div>
            </div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
            <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 14px; text-align: center;">
                <div style="font-size: 24px; font-weight: 700;">{{ $orderCount }}</div>
                <div style="font-size: 12px; color: rgba(255,255,255,0.6); margin-top: 2px;">我的订单</div>
            </div>
            <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 14px; text-align: center;">
                <div style="font-size: 24px; font-weight: 700;">{{ $sellCount }}</div>
                <div style="font-size: 12px; color: rgba(255,255,255,0.6); margin-top: 2px;">在售书籍</div>
            </div>
            <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 14px; text-align: center;">
                <div style="font-size: 24px; font-weight: 700;">{{ $cartCount }}</div>
                <div style="font-size: 12px; color: rgba(255,255,255,0.6); margin-top: 2px;">购物车</div>
            </div>
        </div>
    </div>

    <div style="background: #ffffff; border-radius: 12px; border: 1px solid #cec4b0; padding: 4px 0;">
        <a href="/orders" style="display: flex; align-items: center; padding: 14px 20px; font-size: 15px; color: #2c2416; border-bottom: 1px solid #cec4b0; text-decoration: none;">
            <span style="margin-right: 10px;">📋</span> 我的订单 <span style="margin-left: auto; color: #cec4b0;">›</span>
        </a>
        <a href="/my-sells" style="display: flex; align-items: center; padding: 14px 20px; font-size: 15px; color: #2c2416; border-bottom: 1px solid #cec4b0; text-decoration: none;">
            <span style="margin-right: 10px;">📖</span> 我的卖书 <span style="margin-left: auto; color: #cec4b0;">›</span>
        </a>
        <a href="/wants?mine=1" style="display: flex; align-items: center; padding: 14px 20px; font-size: 15px; color: #2c2416; border-bottom: 1px solid #cec4b0; text-decoration: none;">
            <span style="margin-right: 10px;">🔍</span> 我的求购 <span style="margin-left: auto; color: #cec4b0;">›</span>
        </a>
        <a href="/cart" style="display: flex; align-items: center; padding: 14px 20px; font-size: 15px; color: #2c2416; border-bottom: 1px solid #cec4b0; text-decoration: none;">
            <span style="margin-right: 10px;">🛒</span> 购物车 <span style="margin-left: auto; color: #cec4b0;">›</span>
        </a>
        <a href="/sell" style="display: flex; align-items: center; padding: 14px 20px; font-size: 15px; color: #2c2416; border-bottom: 1px solid #cec4b0; text-decoration: none;">
            <span style="margin-right: 10px;">✦</span> 我要卖书 <span style="margin-left: auto; color: #cec4b0;">›</span>
        </a>
        <a href="/wants" style="display: flex; align-items: center; padding: 14px 20px; font-size: 15px; color: #2c2416; text-decoration: none;">
            <span style="margin-right: 10px;">🔍</span> 求购广场 <span style="margin-left: auto; color: #cec4b0;">›</span>
        </a>
    </div>
</div>
@endsection
