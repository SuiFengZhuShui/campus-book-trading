@if($wants->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        @foreach($wants as $want)
            <a href="/wants/{{ $want->id }}" style="display: block; background: #ffffff; border-radius: 12px; padding: 20px; border: 1px solid rgba(26,31,43,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: box-shadow 0.2s, border-color 0.2s;">
                <div style="font-size: 15px; font-weight: 600; color: #2c2416; line-height: 1.4; min-height: 42px; margin-bottom: 8px;">{{ $want->title }}</div>
                <div style="font-size: 13px; color: #6e6559; min-height: 20px; margin-bottom: 4px;">{{ $want->author ?: '—' }} / {{ $want->publisher ?: '—' }}</div>
                <div style="font-size: 12px; color: #b49450; min-height: 18px; margin-bottom: 12px;">{{ $want->category->name ?? '学院未指定' }}</div>
                <div style="display: flex; justify-content: space-between; align-items: center; min-height: 28px;">
                    <span style="font-size: 18px; font-weight: 700; color: #b49450;">最高 ¥{{ $want->max_price }}</span>
                    <span style="font-size: 12px; color: #6e6559;">{{ $want->user->name ?? '' }}</span>
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
