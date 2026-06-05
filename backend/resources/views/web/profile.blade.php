@extends('web.layouts.app')

@section('title', '个人中心 - 校园二手书网')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">

    {{-- 用户头部 --}}
    <div style="position: relative; border-radius: 16px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(26,31,43,0.08);">
        <div style="background: linear-gradient(160deg, #9a7b2e 0%, #9a7b2e 50%, #3b6ba8 100%); padding: 36px 24px 28px; text-align: center;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(255,255,255,0.18); border: 3px solid rgba(255,255,255,0.3); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;">
                <span style="font-size: 30px; color: #fff; font-weight: 700;">{{ mb_substr($user->name, 0, 1) }}</span>
            </div>
            <div style="font-size: 20px; color: #fff; font-weight: 700;">{{ $user->name }}</div>
            <div style="font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 4px;">学号 {{ $user->student_id ?: '未设置' }}</div>
            <div style="font-size: 12px; color: rgba(255,255,255,0.65);">{{ substr($user->phone, 0, 3) . '****' . substr($user->phone, 7) }}</div>
        </div>
        <a href="/profile?edit=1" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 16px; color: #fff;">✎</a>
    </div>

    {{-- 编辑个人信息 --}}
    @if(request('edit'))
    <div style="background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #c9c0ae; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <div style="font-size: 15px; font-weight: 600; color: #2c2416; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid rgba(180,148,80,0.25);">编辑个人信息</div>
        <form method="POST" action="/profile">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: #595045; margin-bottom: 4px;">姓名</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #c9c0ae; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: #595045; margin-bottom: 4px;">学号</label>
                <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}" style="width: 100%; padding: 10px 12px; border: 1px solid #c9c0ae; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: #595045; margin-bottom: 4px;">手机号</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid #c9c0ae; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>
            <button type="submit" style="width: 100%; height: 44px; background: linear-gradient(135deg, #9a7b2e, #c5a85a); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer;">保存修改</button>
        </form>
    </div>
    @endif

    {{-- 订单统计卡片 --}}
    <div style="display: flex; gap: 8px; margin-bottom: 16px;">
        <a href="/orders?status=pending" style="flex: 1; background: #ffffff; border-radius: 12px; padding: 16px 8px; text-align: center; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <div style="font-size: 22px; font-weight: 700; color: #b0822c;">{{ $stats['order_pending'] }}</div>
            <div style="font-size: 11px; color: #595045; margin-top: 4px;">待付款</div>
        </a>
        <a href="/orders?status=paid" style="flex: 1; background: #ffffff; border-radius: 12px; padding: 16px 8px; text-align: center; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <div style="font-size: 22px; font-weight: 700; color: #9a7b2e;">{{ $stats['order_paid'] }}</div>
            <div style="font-size: 11px; color: #595045; margin-top: 4px;">已付款</div>
        </a>
        <a href="/orders?status=confirmed" style="flex: 1; background: #ffffff; border-radius: 12px; padding: 16px 8px; text-align: center; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <div style="font-size: 22px; font-weight: 700; color: #5b7fbd;">{{ $stats['order_confirmed'] }}</div>
            <div style="font-size: 11px; color: #595045; margin-top: 4px;">待取书</div>
        </a>
        <a href="/orders?status=picked_up" style="flex: 1; background: #ffffff; border-radius: 12px; padding: 16px 8px; text-align: center; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <div style="font-size: 22px; font-weight: 700; color: #2d6a4f;">{{ $stats['order_done'] }}</div>
            <div style="font-size: 11px; color: #595045; margin-top: 4px;">已完成</div>
        </a>
    </div>

    {{-- 菜单分组 --}}
    <div style="margin-bottom: 12px;">
        <div style="font-size: 12px; color: #595045; padding: 8px 4px;">交易管理</div>
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid #c9c0ae; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden;">
            <a href="/orders" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; text-decoration: none; color: #2c2416;">
                <div style="display: flex; align-items: center; gap: 10px;"><span style="font-size: 16px;">📋</span><span style="font-size: 15px;">我的订单</span></div>
                <span style="font-size: 18px; color: #d0cdc8;">›</span>
            </a>
            <div style="height: 1px; background: #c9c0ae; margin: 0 16px;"></div>
            <a href="/my-sells" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; text-decoration: none; color: #2c2416;">
                <div style="display: flex; align-items: center; gap: 10px;"><span style="font-size: 16px;">📖</span><span style="font-size: 15px;">我的卖书</span></div>
                <span style="font-size: 18px; color: #d0cdc8;">›</span>
            </a>
            <div style="height: 1px; background: #c9c0ae; margin: 0 16px;"></div>
            <a href="/wants?mine=1" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; text-decoration: none; color: #2c2416;">
                <div style="display: flex; align-items: center; gap: 10px;"><span style="font-size: 16px;">🔍</span><span style="font-size: 15px;">我的求购</span></div>
                <span style="font-size: 18px; color: #d0cdc8;">›</span>
            </a>
        </div>
    </div>

    <div style="margin-bottom: 12px;">
        <div style="font-size: 12px; color: #595045; padding: 8px 4px;">更多</div>
        <div style="background: #ffffff; border-radius: 12px; border: 1px solid #c9c0ae; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden;">
            <a href="/sell" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; text-decoration: none; color: #2c2416;">
                <div style="display: flex; align-items: center; gap: 10px;"><span style="font-size: 16px;">✏️</span><span style="font-size: 15px;">我要卖书</span></div>
                <span style="font-size: 18px; color: #d0cdc8;">›</span>
            </a>
            <div style="height: 1px; background: #c9c0ae; margin: 0 16px;"></div>
            <a href="/post-want" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; text-decoration: none; color: #2c2416;">
                <div style="display: flex; align-items: center; gap: 10px;"><span style="font-size: 16px;">📝</span><span style="font-size: 15px;">发布求购</span></div>
                <span style="font-size: 18px; color: #d0cdc8;">›</span>
            </a>
        </div>
    </div>

    {{-- 退出登录 --}}
    <form method="POST" action="/logout" style="margin-bottom: 40px;">
        @csrf
        <button type="submit" style="width: 100%; padding: 12px; border: 1px solid #c9c0ae; border-radius: 10px; color: #595045; font-size: 14px; background: #fff; cursor: pointer;">退出登录</button>
    </form>
</div>
@endsection
