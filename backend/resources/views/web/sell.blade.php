@extends('web.layouts.app')

@section('title', '卖书 - 校园二手书网')

@section('content')
<div style="max-width: 640px; margin: 0 auto;">
    <a href="/" style="display:inline-flex;align-items:center;gap:4px;padding:8px 18px;background:linear-gradient(135deg,#b49450,#d4bc7c);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;margin-bottom:20px;cursor:pointer;">&larr; 返回首页</a>
    <div class="form-card">
        <h1 class="form-title" style="text-align: left;">提交卖书信息</h1>

        <div class="alert-error" style="display:none;"></div>

        <form id="sell-form" action="/sell" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label><span class="required">*</span> 书名</label>
                <input type="text" name="title" value="{{ old('title', $prefill['title'] ?? '') }}" placeholder="请输入教材名称" required maxlength="200">
            </div>

            <div class="form-group">
                <label><span class="required">*</span> 作者</label>
                <input type="text" name="author" value="{{ old('author', $prefill['author'] ?? '') }}" placeholder="请输入作者" required maxlength="100">
            </div>

            <div class="form-group">
                <label><span class="required">*</span> 出版社</label>
                <input type="text" name="publisher" value="{{ old('publisher', $prefill['publisher'] ?? '') }}" placeholder="请输入出版社" required maxlength="100">
            </div>

            <div class="form-group">
                <label>书号</label>
                <input type="text" name="isbn" value="{{ old('isbn') }}" placeholder="选填" maxlength="20">
            </div>

            <div class="form-group">
                <label><span class="required">*</span> 所属学院</label>
                <select name="category_id" required>
                    <option value="">请选择学院</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (old('category_id') ?: ($prefill['category_id'] ?? '')) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label><span class="required">*</span> 新旧程度</label>
                <select name="condition" required>
                    <option value="">请选择</option>
                    <option value="like_new" {{ old('condition') == 'like_new' ? 'selected' : '' }}>全新</option>
                    <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>几乎全新</option>
                    <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>正常使用</option>
                    <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>较旧</option>
                </select>
            </div>

            <div class="form-group">
                <label><span class="required">*</span> 原价</label>
                <input type="number" name="original_price" value="{{ old('original_price') }}" placeholder="请输入原价" required min="0.01" step="0.01">
            </div>

            <div class="form-group">
                <label>补充说明</label>
                <textarea name="description" placeholder="选填，最多500字" maxlength="500">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label><span class="required">*</span> 书籍照片（2-5张，至少封面+内页/背面）</label>
                <div class="file-upload-area" id="upload-trigger">
                    <div class="icon">📷</div>
                    <p>点击上传照片</p>
                </div>
                <input type="file" name="images[]" id="file-input" accept="image/jpeg,image/png,image/jpg,image/webp" multiple style="display:none;">
                <div class="preview-grid" id="preview-grid"></div>
                <div class="hint">支持 JPG、PNG、WebP，单张 ≤ 5MB</div>
            </div>

            <button type="submit" class="btn-amber" id="submit-btn" style="width:100%; height:46px; border-radius:10px;">提交审核</button>
        </form>
    </div>
</div>

<div id="toast" class="toast"></div>

<style>
.toast { position: fixed; top: 24px; left: 50%; transform: translateX(-50%); z-index: 9999; padding: 14px 28px; border-radius: 10px; font-size: 15px; font-weight: 500; box-shadow: 0 8px 24px rgba(0,0,0,0.15); opacity: 0; transition: opacity 0.3s ease; pointer-events: none; }
.toast.show { opacity: 1; }
.toast.success { background: #d4edda; color: #2d6a4f; }
.toast.error { background: #fee2e2; color: #bc4742; }
</style>

<script>
    let filesArray = [];
    const MAX_FILES = 5;

    document.getElementById('upload-trigger').addEventListener('click', function() {
        document.getElementById('file-input').click();
    });

    document.getElementById('file-input').addEventListener('change', function(e) {
        addFiles(Array.from(e.target.files));
        this.value = '';
    });

    function addFiles(newFiles) {
        var remaining = MAX_FILES - filesArray.length;
        var toAdd = newFiles.slice(0, remaining);
        filesArray = filesArray.concat(toAdd);
        renderPreviews();
    }

    function removeFile(index) {
        filesArray.splice(index, 1);
        renderPreviews();
    }

    function renderPreviews() {
        var grid = document.getElementById('preview-grid');
        grid.innerHTML = '';
        filesArray.forEach(function(file, i) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var item = document.createElement('div');
                item.className = 'preview-item';
                item.innerHTML = '<img src="' + e.target.result + '"><button type="button" class="remove-btn" data-idx="' + i + '">&times;</button>';
                grid.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
        attachRemoveEvents();
    }

    function attachRemoveEvents() {
        document.querySelectorAll('.remove-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                removeFile(parseInt(this.dataset.idx));
            });
        });
    }

    function showToast(msg, type) {
        var t = document.getElementById('toast');
        t.textContent = msg;
        t.className = 'toast ' + type + ' show';
        setTimeout(function() { t.classList.remove('show'); }, 3000);
    }

    function resetErrors() {
        document.querySelectorAll('.field-err').forEach(function(el) { el.remove(); });
        document.querySelectorAll('input, select, textarea').forEach(function(el) { el.style.borderColor = ''; });
        var box = document.querySelector('.alert-error');
        box.style.display = 'none';
        box.innerHTML = '';
    }

    document.querySelector('#sell-form').addEventListener('submit', function(e) {
        e.preventDefault();
        if (filesArray.length < 2) {
            alert('请至少上传 2 张照片（封面+内页/背面）');
            return;
        }
        resetErrors();
        var btn = document.getElementById('submit-btn');
        btn.disabled = true; btn.textContent = '提交中...';
        var fd = new FormData(e.target);
        filesArray.forEach(function(f) { fd.append('images[]', f); });
        fetch(e.target.action, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': fd.get('_token') } })
            .then(function(r) {
                return r.json().then(function(d) {
                    if (!r.ok) {
                        if (d.errors) {
                            Object.keys(d.errors).forEach(function(field) {
                                var el = document.querySelector('[name="' + field + '"]');
                                if (el) {
                                    el.style.borderColor = '#bc4742';
                                    var err = document.createElement('div');
                                    err.className = 'field-err';
                                    err.style.cssText = 'color:#bc4742;font-size:12px;margin-top:4px;';
                                    err.textContent = d.errors[field][0];
                                    el.parentNode.appendChild(err);
                                }
                            });
                        }
                        var box = document.querySelector('.alert-error');
                        box.style.display = 'none';
                        showToast(d.message || '提交失败', 'error');
                        btn.disabled = false; btn.textContent = '提交审核';
                        throw new Error('validation failed');
                    }
                    // 提交成功
                    document.querySelector('#sell-form').reset();
                    filesArray = [];
                    document.getElementById('preview-grid').innerHTML = '';
                    showToast('提交成功！平台会在1-2个工作日内审核。', 'success');
                    btn.disabled = false; btn.textContent = '提交审核';
                });
            })
            .catch(function(e) {
                if (e.message !== 'validation failed') {
                    showToast('网络错误，请检查网络连接后重试', 'error');
                }
                btn.disabled = false; btn.textContent = '提交审核';
            });
    });
</script>
@endsection
