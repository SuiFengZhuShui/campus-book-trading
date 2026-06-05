@extends('web.layouts.app')

@section('title', '确认购买 - 校园二手书网')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <h2 style="font-size: 22px; font-weight: 600; color: #2c2416; letter-spacing: -0.01em; margin-bottom: 24px;">确认购买</h2>

    {{-- 书籍信息 --}}
    <div style="background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #cec4b0; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 24px; display: flex; gap: 20px;">
        @php $cover = $book->images->where('type', 'cover')->first(); @endphp
        @if($cover)
            <img src="{{ asset('storage/' . $cover->path) }}" alt="{{ $book->title }}"
                 style="width: 100px; height: 140px; object-fit: cover; border-radius: 8px; flex-shrink: 0;">
        @else
            <div style="width: 100px; height: 140px; border-radius: 8px; background: #f4f1ec; display: flex; align-items: center; justify-content: center; font-size: 40px; flex-shrink: 0;">📖</div>
        @endif
        <div>
            <div style="font-size: 16px; font-weight: 600; color: #2c2416; margin-bottom: 6px;">{{ $book->title }}</div>
            <div style="font-size: 13px; color: #6e6559; margin-bottom: 4px;">{{ $book->author }} / {{ $book->publisher }}</div>
            <div style="font-size: 13px; color: #6e6559; margin-bottom: 8px;">{{ $book->category->name ?? '' }}</div>
            <div style="font-size: 24px; color: #b49450; font-weight: 700;">¥{{ $book->price }}</div>
        </div>
    </div>

    {{-- 下单表单 --}}
    <div style="background: #ffffff; border-radius: 12px; padding: 24px; border: 1px solid #cec4b0; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <form method="POST" action="/buy/{{ $book->id }}">
            @csrf

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="form-group">
                <label>取书地点 <span class="required">*</span></label>
                <select name="pickup_location" required>
                    <option value="">请选择取书地点</option>
                    <option value="图书馆一楼大厅">图书馆一楼大厅</option>
                    <option value="一食堂门口">一食堂门口</option>
                    <option value="二食堂门口">二食堂门口</option>
                    <option value="教学楼A区大厅">教学楼A区大厅</option>
                    <option value="教学楼B区大厅">教学楼B区大厅</option>
                    <option value="学生活动中心">学生活动中心</option>
                </select>
                @error('pickup_location')
                    <div style="color:#bc4742;font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="background: rgba(180,148,80,0.06); border-radius: 8px; padding: 16px; margin-bottom: 20px; font-size: 13px; color: #6e6559; border-left: 3px solid #b49450;">
                <p style="margin: 0 0 6px 0; font-weight: 500; color: #2c2416;">下单须知：</p>
                <p style="margin: 0 0 2px 0;">- 下单后请在<strong>30分钟内</strong>完成支付，超时订单将自动取消</p>
                <p style="margin: 0 0 2px 0;">- 支付后平台确认，凭订单号到取书地点拿书</p>
                <p style="margin: 0;">- 书籍售出后不可退货，请仔细确认</p>
            </div>

            <div style="display: flex; gap: 12px;">
                <a href="/books/{{ $book->id }}" class="btn-ghost" style="flex: 1; height: 46px; font-size: 16px;">返回</a>
                <button type="submit" class="btn-amber" style="flex: 2; height: 46px; font-size: 16px;">确认下单</button>
            </div>
        </form>
    </div>
</div>
@endsection
