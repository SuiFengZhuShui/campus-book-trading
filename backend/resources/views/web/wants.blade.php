@extends('web.layouts.app')

@section('title', request('mine') ? '我的求购 - 校园二手书网' : '求购广场 - 校园二手书网')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="font-size: 22px; font-weight: 600; color: #2c2416; letter-spacing: -0.01em; margin: 0; padding-left: 14px; border-left: 4px solid #b49450;">{{ request('mine') ? '我的求购' : '求购广场' }}</h2>
    <div style="display:flex;gap:8px;">
        @auth
        @if(request('mine'))
        <a href="/wants" style="display:inline-flex;align-items:center;gap:4px;padding:10px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;">全部求购</a>
        @else
        <a href="/wants?mine=1" style="display:inline-flex;align-items:center;gap:4px;padding:10px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;">我的求购</a>
        @endif
        @endauth
        <a href="/post-want" style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:500;text-decoration:none;">发布求购</a>
    </div>
</div>

<form action="/wants" method="GET" id="want-search-form" style="display:flex;gap:8px;margin-bottom:20px;">
    @if(request('mine'))
    <input type="hidden" name="mine" value="1" id="want-mine-input">
    @endif
    <div style="flex:1;position:relative;">
        <input type="text" name="keyword" id="want-search-input" placeholder="搜索求购书名、作者…" value="{{ request('keyword') }}"
            style="width:100%;height:42px;border:1px solid #e5dccf;border-radius:8px;padding:0 40px 0 14px;font-size:14px;background:#fff;box-sizing:border-box;">
        <span id="want-clear-search"
            style="position:absolute;right:6px;top:50%;transform:translateY(-50%);width:24px;height:24px;background:#d4bc7c;color:#fff;border-radius:50%;display:{{ request('keyword') ? 'flex' : 'none' }};align-items:center;justify-content:center;font-size:14px;cursor:pointer;z-index:2;line-height:1;">×</span>
    </div>
    <button type="submit" style="height:42px;padding:0 20px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;white-space:nowrap;">搜索求购</button>
</form>
<script>
    var wantInput = document.getElementById('want-search-input');
    var wantClear = document.getElementById('want-clear-search');
    wantInput.addEventListener('input', function() {
        wantClear.style.display = this.value.length > 0 ? 'flex' : 'none';
    });
    wantClear.addEventListener('click', function() { doWantSearch(''); });
    document.getElementById('want-search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var kw = wantInput.value;
        doWantSearch(kw);
    });
    function doWantSearch(keyword) {
        wantInput.value = keyword;
        wantClear.style.display = keyword ? 'flex' : 'none';
        var qs = keyword ? '?keyword=' + encodeURIComponent(keyword) : '';
        var mine = document.getElementById('want-mine-input');
        if (mine) qs += (qs ? '&' : '?') + 'mine=1';
        var base = '/wants';
        history.pushState({}, '', base + qs);
        fetch(base + qs, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json() })
            .then(function(d) { document.getElementById('want-results').innerHTML = d.html; });
    }
</script>

<div id="want-results">
@include('web.partials.want-list')
</div>
@endsection
