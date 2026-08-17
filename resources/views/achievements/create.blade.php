@extends('layouts.app')

@section('title', 'إضافة إنجاز جديد')

@section('content')
    <section class="section">
        <div class="container detail-wrap" style="max-width: 760px;">
            <div class="detail-header">
                <a href="{{ $preselected ? route('departments.show', $preselected->slug) : route('home') }}" class="back-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    العودة
                </a>
                <h1 class="detail-title">إضافة إنجاز جديد</h1>
                <p style="color: var(--muted); font-size: 0.9rem;">
                    املأ بيانات الإنجاز، ثم أرفق صورة الغلاف والصور والفيديوهات والملفات إن وُجدت.
                </p>
            </div>

            @if ($errors->any())
                <ul class="field-errors">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('achievements.store') }}" enctype="multipart/form-data" class="detail-card" id="achievement-form" novalidate>
                @csrf

                @php
                    $isAdmin = auth()->user()->isAdmin();
                    $lockedDepartment = ! $isAdmin && auth()->user()->department_id !== null;
                @endphp

                <div class="form-section">
                    <h4>البيانات الأساسية</h4>
                    <p>العنوان والوصف والتاريخ والقسم.</p>

                    <div class="form-group">
                        <label for="title">عنوان الإنجاز <span class="req">*</span></label>
                        <input id="title" class="form-input" type="text" name="title" value="{{ old('title') }}" required autofocus maxlength="255" placeholder="مثال: ترقية الشبكة الأساسية للمصلحة">
                        @error('title')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-error field-msg" hidden></p>
                    </div>

                    <div class="form-group">
                        <label for="description">وصف الإنجاز <span class="req">*</span></label>
                        <textarea id="description" class="form-input" name="description" rows="5" required placeholder="اكتب تفاصيل الإنجاز والنتائج المتحققة...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-error field-msg" hidden></p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="achievement_at">تاريخ الإنجاز <span class="req">*</span></label>
                            <input id="achievement_at" class="form-input" type="datetime-local" name="achievement_at" value="{{ old('achievement_at') }}" required>
                            @error('achievement_at')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-error field-msg" hidden></p>
                        </div>

                        <div class="form-group">
                            <label for="department_id">القسم <span class="req">*</span></label>
                            @if ($lockedDepartment)
                                <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                                <input class="form-input" type="text" value="{{ auth()->user()->department?->name }}" disabled>
                            @else
                                <select id="department_id" class="form-input" name="department_id" required>
                                    <option value="">— اختر القسم —</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id || ($preselected && $preselected->id === $department->id))>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="form-error field-msg" hidden></p>
                            @endif
                            @error('department_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4>المرفقات (اختياري)</h4>
                    <p>أضف صورة غلاف للإنجاز وصوراً وفيديوهات وملفات PDF (حتى 1GB للملفات، و200MB لملفات PDF).</p>

                    <div class="upload-group">
                        <label for="thumbnail">صورة الغلاف</label>
                        <input id="thumbnail" class="form-input upload-input" type="file" name="thumbnail" accept="image/*">
                        <button type="button" class="upload-trigger" data-upload-trigger="thumbnail">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M12 5v14M5 12h14"/></svg>
                            اختيار صورة الغلاف
                        </button>
                        <div class="upload-slider" data-upload-list="thumbnail"></div>
                        @error('thumbnail')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-error field-msg" hidden></p>
                    </div>

                    <div class="upload-group">
                        <label for="images">صور إضافية</label>
                        <input id="images" class="form-input upload-input" type="file" name="images[]" accept="image/*" multiple>
                        <button type="button" class="upload-trigger" data-upload-trigger="images">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M12 5v14M5 12h14"/></svg>
                            اختيار الصور
                        </button>
                        <div class="upload-slider" data-upload-list="images"></div>
                        @error('images.*')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-error field-msg" hidden></p>
                    </div>

                    <div class="upload-group">
                        <label for="videos">فيديوهات</label>
                        <input id="videos" class="form-input upload-input" type="file" name="videos[]" accept="video/*" multiple>
                        <button type="button" class="upload-trigger" data-upload-trigger="videos">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M12 5v14M5 12h14"/></svg>
                            اختيار الفيديوهات
                        </button>
                        <div class="upload-slider" data-upload-list="videos"></div>
                        @error('videos.*')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-error field-msg" hidden></p>
                    </div>

                    <div class="upload-group">
                        <label for="pdfs">ملفات PDF</label>
                        <input id="pdfs" class="form-input upload-input" type="file" name="pdfs[]" accept="application/pdf" multiple>
                        <button type="button" class="upload-trigger" data-upload-trigger="pdfs">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M12 5v14M5 12h14"/></svg>
                            اختيار ملفات PDF
                        </button>
                        <div class="upload-grid" data-upload-list="pdfs"></div>
                        @error('pdfs.*')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-error field-msg" hidden></p>
                    </div>
                </div>

                <div class="form-section">
                    <h4>الفريق المساهم (اختياري)</h4>
                    <p>اسم واحد في كل سطر.</p>

                    <div class="form-group">
                        <textarea id="employees" class="form-input" name="employees" rows="3" placeholder="مثال:&#10;م. أحمد السويعي&#10;م. فاطمة الزنتاني">{{ old('employees') }}</textarea>
                    </div>
                </div>

                <div class="auth-actions">
                    <button type="submit" class="btn btn-primary btn-block">حفظ الإنجاز</button>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
<style>
    .req { color: var(--danger); }
    .form-error.field-msg { color: var(--danger); }

    .upload-input { display: none; }
    .upload-group {
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        background: var(--card);
        padding: 18px;
        margin-top: 14px;
    }
    .upload-group:first-of-type {
        margin-top: 6px;
    }
    .upload-group > label {
        display: block;
        font-weight: 800;
        color: var(--navy-800);
        margin-bottom: 12px;
    }
    .upload-trigger {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 1px dashed var(--border);
        border-radius: var(--radius-sm);
        background: var(--bg-alt);
        color: var(--navy-700);
        font-family: inherit;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: background var(--t-fast), border-color var(--t-fast), color var(--t-fast);
    }
    .upload-trigger:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: rgba(204, 55, 59, 0.05);
    }

    /* Horizontal scrolling slider for images & videos */
    .upload-slider {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding: 4px 2px 10px;
        margin-top: 12px;
        scroll-snap-type: x proximity;
        scrollbar-width: thin;
    }
    .upload-slider::-webkit-scrollbar {
        height: 8px;
    }
    .upload-slider::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: var(--radius-full);
    }
    .upload-slider:empty {
        display: none;
    }
    .upload-slider .upload-card {
        flex: 0 0 auto;
        width: 200px;
    }

    /* Grid for PDFs (2 per row) */
    .upload-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 12px;
    }
    .upload-grid:empty {
        display: none;
    }
    @media (max-width: 640px) {
        .upload-grid {
            grid-template-columns: 1fr;
        }
    }

    .upload-card {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 8px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--bg-alt);
    }
    .upload-preview {
        height: 92px;
        border-radius: 6px;
        overflow: hidden;
        background: #0d1626;
        display: grid;
        place-items: center;
    }
    .upload-preview img,
    .upload-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .upload-pdf {
        color: #fff;
        font-weight: 800;
        font-size: 0.78rem;
        letter-spacing: 0.08em;
    }

    /* PDF row card */
    .upload-card.pdf-card {
        flex-direction: row;
        align-items: center;
        gap: 12px;
        min-width: 0;
        overflow: hidden;
    }
    .upload-card.pdf-card .upload-preview {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 8px;
    }
    .upload-grid .upload-card {
        min-width: 0;
        overflow: hidden;
    }
    .upload-meta {
        min-width: 0;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
        overflow: hidden;
    }
    .upload-name {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--navy-800);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .upload-size {
        font-size: 0.72rem;
        color: var(--muted);
    }
    .upload-actions {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
        flex-shrink: 0;
    }
    .upload-open,
    .upload-del {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--navy-700);
        cursor: pointer;
        transition: background var(--t-fast), color var(--t-fast), border-color var(--t-fast);
    }
    .upload-open:hover {
        color: var(--accent);
        border-color: var(--accent);
    }
    .upload-del {
        color: var(--danger);
        border-color: #f0c4c6;
    }
    .upload-del:hover {
        background: #fbecec;
    }
</style>
<script>
    (function () {
        'use strict';

        var form = document.getElementById('achievement-form');

        var MB = 1024 * 1024;
        var MB_TO_FILES = { thumbnail: 1024, images: 1024, videos: 1024, pdfs: 200 };

        function formatSize(bytes) {
            if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + 'GB';
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + 'MB';
            if (bytes >= 1024) return Math.round(bytes / 1024) + 'KB';
            return bytes + 'B';
        }

        var uploadGroups = ['thumbnail', 'images', 'videos', 'pdfs'];

        function setupUpload(group, multiple) {
            var input = document.getElementById(group);
            if (!input) return;
            var list = document.querySelector('[data-upload-list="' + group + '"]');
            var trigger = document.querySelector('[data-upload-trigger="' + group + '"]');
            var files = [];
            var type = (group === 'images' || group === 'thumbnail') ? 'image' : group === 'videos' ? 'video' : 'pdf';

            if (trigger) {
                trigger.addEventListener('click', function (e) {
                    e.preventDefault();
                    input.click();
                });
            }

            function sync() {
                var dt = new DataTransfer();
                files.forEach(function (f) { dt.items.add(f); });
                input.files = dt.files;
            }

            function render() {
                list.innerHTML = '';
                files.forEach(function (f, idx) {
                    var url = URL.createObjectURL(f);
                    var isPdf = group === 'pdfs';
                    var preview;
                    if (isPdf) preview = '<span class="upload-pdf">PDF</span>';
                    else if (type === 'image') preview = '<img src="' + url + '" alt="">';
                    else preview = '<video src="' + url + '" preload="metadata" muted></video>';
                    var card = document.createElement('div');
                    card.className = 'upload-card' + (isPdf ? ' pdf-card' : '');
                    card.innerHTML =
                        '<div class="upload-preview">' + preview + '</div>' +
                        '<div class="upload-meta">' +
                            '<span class="upload-name"></span>' +
                            '<span class="upload-size"></span>' +
                        '</div>' +
                        '<div class="upload-actions">' +
                            '<a class="upload-open" href="' + url + '" target="_blank" rel="noopener" title="فتح">' +
                                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14L21 3"/></svg>' +
                            '</a>' +
                            '<button type="button" class="upload-del" data-idx="' + idx + '" title="حذف">' +
                                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:15px;height:15px"><path d="M18 6L6 18M6 6l12 12"/></svg>' +
                            '</button>' +
                        '</div>';
                    card.querySelector('.upload-name').textContent = f.name;
                    card.querySelector('.upload-size').textContent = formatSize(f.size);
                    list.appendChild(card);
                });
            }

            function addFiles(fileList) {
                var arr = Array.prototype.slice.call(fileList);
                if (!multiple) files = arr.slice(0, 1);
                else files = files.concat(arr);
                sync();
                render();
            }

            input.addEventListener('change', function () { addFiles(input.files); });

            list.addEventListener('click', function (e) {
                var del = e.target.closest('.upload-del');
                if (del) {
                    files.splice(parseInt(del.dataset.idx, 10), 1);
                    sync();
                    render();
                    showError(input);
                }
            });

            render();
        }

        uploadGroups.forEach(function (group) { setupUpload(group, group !== 'thumbnail'); });

        function rule(field) {
            var map = {
                title: function (v) { return v.trim().length > 0 ? '' : 'يرجى كتابة عنوان الإنجاز.'; },
                description: function (v) { return v.trim().length > 0 ? '' : 'يرجى كتابة وصف الإنجاز.'; },
                achievement_at: function (v) {
                    if (v === '') return 'يرجى تحديد تاريخ الإنجاز.';
                    var d = new Date(v);
                    var today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (!isNaN(d.getTime()) && d.getTime() > today.getTime()) {
                        return 'يجب ألا يتجاوز تاريخ الإنجاز تاريخ اليوم';
                    }
                    return '';
                },
                department_id: function (v) { return v !== '' ? '' : 'يرجى اختيار القسم.'; }
            };
            return map[field] || function () { return ''; };
        }

        function fileRule(field, el) {
            var files = el.files;
            if (!files.length) return '';
            var maxMB = MB_TO_FILES[field];
            var isImage = field === 'thumbnail' || field === 'images';
            var isVideo = field === 'videos';
            var isPdf = field === 'pdfs';
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                var okType = isImage ? f.type.indexOf('image/') === 0
                    : isVideo ? f.type.indexOf('video/') === 0
                        : f.type === 'application/pdf' || f.type === 'application/x-pdf';
                if (!okType) {
                    return isPdf ? 'يُسمح بملفات PDF فقط.'
                        : isVideo ? 'يُسمح بملفات الفيديو فقط (MP4, WebM, Ogg, MOV, AVI, MKV).'
                            : 'يُسمح بملفات الصور فقط (JPG, PNG, GIF, WebP...).';
                }
                if (f.size > maxMB * MB) {
                    var mb = Math.ceil(f.size / MB);
                    return 'حجم الملف (' + mb + 'MB) أكبر من الحد المسموح (' + maxMB + 'MB).';
                }
            }
            return '';
        }

        var fileFields = ['thumbnail', 'images', 'videos', 'pdfs'];
        var fileRuleMap = {};
        fileFields.forEach(function (id) {
            fileRuleMap[id] = function (el) { return fileRule(id, el); };
        });

        function messageFor(el) {
            return fileRuleMap[el.id] ? fileRuleMap[el.id](el) : rule(el.id)(el.value);
        }

        function messageEl(el) {
            return el.closest('.form-group').querySelector('.field-msg');
        }

        function showError(el) {
            var msg = messageFor(el);
            var t = messageEl(el);
            if (t) {
                t.textContent = msg;
                t.hidden = msg === '';
            }
            return msg === '';
        }

        function clearIfValid(el) {
            var t = messageEl(el);
            if (t && !t.hidden) showError(el);
        }

        form.addEventListener('blur', function (e) {
            if (e.target && e.target.closest && e.target.closest('.form-group')) showError(e.target);
        }, true);
        form.addEventListener('change', function (e) {
            if (e.target && e.target.closest && e.target.closest('.form-group')) showError(e.target);
        });
        form.addEventListener('input', function (e) {
            if (e.target) clearIfValid(e.target);
        });

        form.addEventListener('submit', function (e) {
            var ok = true;
            var first = null;
            form.querySelectorAll('input, select, textarea').forEach(function (el) {
                if (!el.closest('.form-group')) return;
                if (!showError(el)) {
                    ok = false;
                    if (!first) first = el;
                }
            });
            if (!ok) {
                e.preventDefault();
                if (first) first.focus();
            }
        });
    })();
</script>
@endpush