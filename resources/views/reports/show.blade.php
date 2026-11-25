<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
    <title>{{ $reportTypeName }} · {{ $periodName }} · {{ $reportNumber }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Noto+Naskh+Arabic:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 16mm 14mm 18mm;

            @bottom-center {
                content: "صفحة " counter(page) " من " counter(pages);
                font-family: 'AL-Mohanad', 'Noto Naskh Arabic', serif;
                font-size: 8.5pt;
                color: #777;
            }

            @bottom-left {
                content: "{{ $reportNumber }}";
                font-family: 'AL-Mohanad', 'Noto Naskh Arabic', serif;
                font-size: 8.5pt;
                color: #777;
            }
        }

        /* Official font: AL-Mohanad (installed on the device, or from public/fonts) */
        @font-face {
            font-family: 'AL-Mohanad';
            src: local('AL-Mohanad'), local('AL Mohanad'), local('ALMohanad'),
                 url('{{ asset('fonts/AL-Mohanad.ttf') }}') format('truetype');
            font-weight: 400;
        }

        @font-face {
            font-family: 'AL-Mohanad';
            src: local('AL-Mohanad Bold'), local('AL Mohanad Bold'), local('ALMohanadBold'),
                 url('{{ asset('fonts/AL-Mohanad-Bold.ttf') }}') format('truetype');
            font-weight: 700;
        }

        :root {
            --report-font: 'AL-Mohanad', 'Noto Naskh Arabic', 'Traditional Arabic', serif;
            --ink: #1f2328;
            --soft: #5b6068;
            --line: #d6d0c4;
            --line-soft: #ebe6dc;
            --gold: #8a6a36;
            --head-bg: #f4efe6;
            --accent: #b02d33;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--report-font);
            color: var(--ink);
            background: #e9e6e1;
            font-size: 12pt;
            line-height: 1.75;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ---------- Screen toolbar (hidden when printing) ---------- */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 24px;
            background: #1b2940;
            color: #fff;
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            font-size: 10pt;
        }

        .toolbar-actions { display: flex; gap: 10px; }

        .toolbar a,
        .toolbar button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font: inherit;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff;
            background: transparent;
            text-decoration: none;
            cursor: pointer;
        }

        .toolbar svg { width: 16px; height: 16px; }

        /* PDF dropdown: save as file or print */
        .pdf-menu { position: relative; }

        .toolbar .pdf-menu-btn { background: #cc373b; border-color: #cc373b; }
        .toolbar .pdf-menu-btn .caret { width: 14px; height: 14px; }
        .pdf-menu.open .pdf-menu-btn .caret { transform: rotate(180deg); }
        .toolbar .pdf-menu-btn[disabled] { opacity: .75; cursor: progress; }

        .pdf-menu-panel {
            position: absolute;
            top: calc(100% + 8px);
            inset-inline-end: 0;
            min-width: 230px;
            padding: 6px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
            display: none;
            z-index: 20;
        }

        .pdf-menu.open .pdf-menu-panel { display: block; }

        .toolbar .pdf-menu-panel button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #1f2328;
            text-align: right;
            font-weight: 400;
        }

        .toolbar .pdf-menu-panel button:hover,
        .toolbar .pdf-menu-panel button:focus-visible { background: #f4efe6; outline: none; }
        .toolbar .pdf-menu-panel svg { width: 20px; height: 20px; color: #8a6a36; flex-shrink: 0; }
        .pdf-menu-panel span { display: flex; flex-direction: column; line-height: 1.4; }
        .pdf-menu-panel b { font-size: 10pt; }
        .pdf-menu-panel small { font-size: 8.5pt; color: #6b7078; }

        /* Copy of the sheet used to build the PDF file (the visible page is never changed) */
        .sheet.sheet-export {
            width: 186mm;
            min-height: 0;
            margin: 0;
            padding: 0;
            box-shadow: none;
        }

        /* "Preparing the file" overlay */
        .export-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(16, 24, 40, .55);
            font-family: 'IBM Plex Sans Arabic', sans-serif;
        }

        .export-overlay.show { display: flex; }

        .export-box {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 26px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
            font-size: 11pt;
            font-weight: 700;
            color: #1f2328;
        }

        .export-spinner {
            width: 22px;
            height: 22px;
            border: 3px solid #eadfcd;
            border-top-color: #cc373b;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ---------- A4 sheet ---------- */
        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            padding: 16mm 14mm 18mm;
            background: #fff;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.12);
        }

        /* Official letterhead: three balanced columns, all centred on one line */
        .letterhead {
            display: grid;
            grid-template-columns: 1fr 110px 1fr;
            align-items: center;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 2.5px solid var(--gold);
            position: relative;
        }

        .letterhead::after {
            content: '';
            position: absolute;
            inset-inline: 0;
            bottom: -6px;
            height: 1px;
            background: var(--gold);
        }

        .lh-org,
        .lh-meta {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            text-align: center;
            line-height: 1.6;
        }

        .lh-org {
            font-family: var(--report-font);
            font-weight: 700;
            font-size: 12.5pt;
        }

        .lh-org .lh-dept { color: var(--gold); }

        .lh-logo {
            display: grid;
            place-items: center;
        }

        .lh-logo img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        .lh-meta {
            align-items: center;
        }

        /* Labels and values in two aligned columns */
        .lh-meta dl {
            display: grid;
            grid-template-columns: auto auto;
            column-gap: 8px;
            row-gap: 4px;
            text-align: right;
            font-size: 11.5pt;
        }

        .lh-meta dt {
            color: var(--soft);
        }

        .lh-meta dd {
            margin: 0;
            font-weight: 700;
            color: var(--ink);
        }

        /* Title block */
        .title-block {
            text-align: center;
            margin: 26px 0 18px;
        }

        .title-block h1 {
            font-family: var(--report-font);
            font-size: 19.5pt;
            font-weight: 700;
            line-height: 1.5;
        }

        .title-block .sub {
            margin-top: 4px;
            font-size: 12.5pt;
            font-weight: 600;
            color: var(--gold);
        }

        .title-block .range {
            display: inline-block;
            margin-top: 10px;
            padding: 4px 18px;
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: 11pt;
            color: var(--soft);
        }

        /* Sections */
        h2.sec {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14pt;
            font-weight: 700;
            margin: 22px 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--line);
            break-after: avoid;
        }

        h2.sec .num {
            display: inline-grid;
            place-items: center;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: var(--gold);
            color: #fff;
            font-size: 11.5pt;
        }

        p.lead { color: var(--soft); text-align: justify; }

        /* Key figures */
        .figures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 12px 0 4px;
        }

        .figure {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }

        .figure .v { font-size: 19.5pt; font-weight: 700; line-height: 1.3; }
        .figure .l { font-size: 10.5pt; color: var(--soft); }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }

        th, td {
            border: 1px solid var(--line);
            padding: 7px 9px;
            text-align: right;
            vertical-align: top;
        }

        thead th {
            background: var(--head-bg);
            font-weight: 700;
            white-space: nowrap;
        }

        thead { display: table-header-group; }
        tr { break-inside: avoid; }

        td.c, th.c { text-align: center; }
        tfoot td { background: var(--head-bg); font-weight: 700; }

        .share-bar {
            height: 6px;
            border-radius: 3px;
            background: var(--line-soft);
            overflow: hidden;
            margin-top: 4px;
        }

        .share-bar span { display: block; height: 100%; background: var(--gold); }

        /* Department block */
        .dept {
            margin-top: 22px;
        }

        .dept-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 12px;
            background: var(--head-bg);
            border-inline-start: 4px solid var(--dept-color, var(--gold));
            border-radius: 4px;
            margin-bottom: 8px;
            break-after: avoid;
        }

        .dept-head h3 {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13pt;
            font-weight: 700;
        }

        .dept-head h3 svg { width: 18px; height: 18px; color: var(--dept-color, var(--gold)); }
        .dept-head .count { font-size: 10.5pt; color: var(--soft); font-weight: 600; }

        .item-title { font-weight: 700; }
        .item-desc { color: var(--soft); white-space: pre-line; margin-top: 2px; }
        .item-team { font-size: 10.5pt; }

        .item-images {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .item-images img {
            width: 31%;
            max-width: 150px;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            border: 1px solid var(--line);
            border-radius: 4px;
        }

        .empty {
            padding: 12px;
            text-align: center;
            color: var(--soft);
            border: 1px dashed var(--line);
            border-radius: 6px;
            font-size: 11pt;
        }

        /* Signatures */
        .signatures {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            margin-top: 44px;
            break-inside: avoid;
        }

        .sig { text-align: center; font-size: 11.5pt; }
        .sig .t { font-weight: 700; margin-bottom: 34px; }
        .sig .line { border-top: 1px solid var(--ink); width: 70%; margin: 0 auto 4px; }
        .sig .n { color: var(--soft); font-size: 10.5pt; }

        .closing {
            margin-top: 26px;
            padding-top: 8px;
            border-top: 1px solid var(--line-soft);
            font-size: 10pt;
            color: var(--soft);
            text-align: center;
        }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
        }

        @media screen and (max-width: 860px) {
            .sheet { width: auto; margin: 12px; padding: 18px; }
            .figures { grid-template-columns: 1fr; }
            .toolbar { padding: 10px 14px; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <a href="{{ route('reports.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            تقرير آخر
        </a>
        <div class="toolbar-actions">
            <a href="{{ route('reports.word', request()->except('print')) }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="m8.5 12 1.2 5 1.8-4 1.8 4 1.2-5"/></svg>
                تحميل Word
            </a>
            <div class="pdf-menu" id="pdf-menu">
                <button type="button" class="pdf-menu-btn" aria-haspopup="true" aria-expanded="false" id="pdf-menu-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>
                    <span class="pdf-menu-label">PDF</span>
                    <svg class="caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="pdf-menu-panel" role="menu">
                    <button type="button" role="menuitem" data-pdf-action="save">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                        <span><b>حفظ كملف PDF</b><small>تنزيل الملف على جهازك</small></span>
                    </button>
                    <button type="button" role="menuitem" data-pdf-action="print">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M6 14h12v7H6z"/></svg>
                        <span><b>طباعة</b><small>إرسال التقرير إلى الطابعة</small></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <article class="sheet">
        {{-- Letterhead --}}
        <header class="letterhead">
            <div class="lh-org">
                <div>{{ config('site.report.country') }}</div>
                <div>{{ config('site.report.ministry') }}</div>
                <div>{{ config('site.organization_name') }}</div>
                <div class="lh-dept">{{ config('site.department_name') }}</div>
            </div>
            <div class="lh-logo">
                <img src="{{ asset('images/mashlaha-logo.jpg') }}" alt="شعار {{ config('site.organization_name') }}">
            </div>
            <div class="lh-meta">
                <dl>
                    <dt>رقم التقرير:</dt>
                    <dd>{{ $reportNumber }}</dd>
                    <dt>تاريخ الإصدار:</dt>
                    <dd>{{ $issuedAt->translatedFormat('j F Y') }}</dd>
                    <dt>النطاق:</dt>
                    <dd>{{ $selectedDepartment?->name ?? 'جميع الأقسام' }}</dd>
                </dl>
            </div>
        </header>

        {{-- Title --}}
        <div class="title-block">
            <h1>{{ $reportTypeName }} لأعمال {{ $selectedDepartment?->name ?? config('site.department_name') }}</h1>
            <div class="sub">{{ $periodName }}</div>
            <div class="range">
                الفترة من {{ $start->translatedFormat('j F Y') }} إلى {{ $end->translatedFormat('j F Y') }}
            </div>
        </div>

        {{-- 1. Summary --}}
        <h2 class="sec"><span class="num">1</span> الملخص التنفيذي</h2>
        <p class="lead">
            يستعرض هذا التقرير الأعمال المسجلة في أرشيف {{ config('site.department_name') }}
            @if ($selectedDepartment)
                الخاصة بـ{{ $selectedDepartment->name }}
            @else
                لجميع أقسام الإدارة
            @endif
            خلال {{ $periodName }}، وقد بلغ إجمالي الأعمال المنفذة خلال هذه الفترة
            ({{ $total }}) {{ $total >= 3 && $total <= 10 ? 'أعمال' : 'عملاً' }}.
        </p>

        @php
            $activeDepartments = $sections->filter(fn ($s) => $s['achievements']->isNotEmpty())->count();
            $teamSize = $sections->flatMap(fn ($s) => $s['achievements']->flatMap->employees)->unique('id')->count();
        @endphp

        <div class="figures" style="grid-template-columns: repeat({{ $withTeam ? 3 : 2 }}, 1fr)">
            <div class="figure">
                <div class="v">{{ $total }}</div>
                <div class="l">إجمالي الأعمال</div>
            </div>
            <div class="figure">
                <div class="v">{{ $selectedDepartment ? 1 : $activeDepartments.' / '.$sections->count() }}</div>
                <div class="l">{{ $selectedDepartment ? 'القسم المشمول' : 'الأقسام ذات الأعمال' }}</div>
            </div>
            @if ($withTeam)
                <div class="figure">
                    <div class="v">{{ $teamSize }}</div>
                    <div class="l">الموظفون المساهمون</div>
                </div>
            @endif
        </div>

        @unless ($selectedDepartment)
            {{-- 2. Distribution by department --}}
            <h2 class="sec"><span class="num">2</span> توزيع الأعمال حسب الأقسام</h2>
            <table>
                <thead>
                    <tr>
                        <th class="c" style="width: 36px">م</th>
                        <th>القسم</th>
                        <th class="c" style="width: 90px">عدد الأعمال</th>
                        <th style="width: 150px">النسبة من الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sections as $section)
                        @php
                            $count = $section['achievements']->count();
                            $share = $total > 0 ? round($count / $total * 100) : 0;
                        @endphp
                        <tr>
                            <td class="c">{{ $loop->iteration }}</td>
                            <td>{{ $section['department']->name }}</td>
                            <td class="c">{{ $count }}</td>
                            <td>
                                {{ $share }}%
                                <div class="share-bar"><span style="width: {{ $share }}%"></span></div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">الإجمالي</td>
                        <td class="c">{{ $total }}</td>
                        <td>{{ $total > 0 ? '100%' : '0%' }}</td>
                    </tr>
                </tfoot>
            </table>
        @endunless

        {{-- Details --}}
        <h2 class="sec"><span class="num">{{ $selectedDepartment ? 2 : 3 }}</span> تفاصيل الأعمال</h2>

        @foreach ($sections as $section)
            @php $department = $section['department']; @endphp
            <section class="dept" style="--dept-color: {{ $department->color }}">
                <div class="dept-head">
                    <h3>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            @include('partials.department-icon', ['icon' => $department->icon])
                        </svg>
                        {{ $department->name }}
                    </h3>
                    <span class="count">{{ \App\Models\Department::countLabel($section['achievements']->count()) }}</span>
                </div>

                @if ($section['achievements']->isEmpty())
                    <div class="empty">لا توجد أعمال مسجلة لهذا القسم خلال الفترة المحددة.</div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th class="c" style="width: 32px">م</th>
                                <th>العمل</th>
                                <th style="width: 92px">التاريخ</th>
                                @if ($withTeam)
                                    <th style="width: 150px">فريق العمل</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($section['achievements'] as $achievement)
                                <tr>
                                    <td class="c">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="item-title">{{ $achievement->title }}</div>
                                        <div class="item-desc">{{ $achievement->description }}</div>
                                        @if ($withImages && $achievement->media->isNotEmpty())
                                            <div class="item-images">
                                                @foreach ($achievement->media->take(3) as $image)
                                                    <img src="{{ asset('storage/' . $image->url) }}" alt="{{ $achievement->title }}" decoding="async">
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $achievement->achievement_at->translatedFormat('j F Y') }}</td>
                                    @if ($withTeam)
                                        <td class="item-team">
                                            {{ $achievement->employees->pluck('full_name')->join('، ') ?: '—' }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>
        @endforeach

        {{-- Signatures --}}
        <div class="signatures">
            <div class="sig">
                <div class="t">{{ config('site.report.prepared_by_title') }}</div>
                <div class="line"></div>
                <div class="n">{{ config('site.report.prepared_by_name') }}</div>
            </div>
            <div class="sig">
                <div class="t">{{ config('site.report.approved_by_title') }}</div>
                <div class="line"></div>
                <div class="n">الاسم والتوقيع</div>
            </div>
        </div>

        <div class="closing">
            صدر هذا التقرير آلياً من أرشيف أعمال {{ config('site.department_name') }} — {{ config('site.organization_name') }}
        </div>
    </article>

    <div class="export-overlay" id="export-overlay" role="status" aria-live="polite">
        <div class="export-box">
            <span class="export-spinner" aria-hidden="true"></span>
            جارٍ تجهيز ملف PDF… قد يستغرق ذلك بضع ثوانٍ
        </div>
    </div>

    <script>
        (function () {
            'use strict';

            var overlay = document.getElementById('export-overlay');
            var menu = document.getElementById('pdf-menu');
            var btn = document.getElementById('pdf-menu-btn');
                        var fileName = @json($fileName . '.pdf');
            var libUrl = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';

            function setOpen(open) {
                menu.classList.toggle('open', open);
                btn.setAttribute('aria-expanded', String(open));
            }

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                setOpen(!menu.classList.contains('open'));
            });
            document.addEventListener('click', function () { setOpen(false); });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });

            function loadLibrary() {
                if (window.html2pdf) return Promise.resolve();
                return new Promise(function (resolve, reject) {
                    var script = document.createElement('script');
                    script.src = libUrl;
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                });
            }

            function ready() {
                return document.fonts ? document.fonts.ready : Promise.resolve();
            }

            // Lets the browser paint (e.g. the overlay) before heavy work starts.
            function nextFrame() {
                return new Promise(function (resolve) {
                    requestAnimationFrame(function () { setTimeout(resolve, 30); });
                });
            }

            // Builds a real PDF file from an off-screen copy of the report and downloads it.
            function savePdf() {
                if (btn.disabled) return;
                setOpen(false);
                btn.disabled = true;
                overlay.classList.add('show');

                var copy = document.querySelector('.sheet').cloneNode(true);
                copy.classList.add('sheet-export');

                nextFrame().then(ready).then(loadLibrary).then(nextFrame).then(function () {
                    return window.html2pdf()
                        .set({
                            margin: [14, 12, 16, 12],
                            filename: fileName,
                            image: { type: 'jpeg', quality: 0.92 },
                            html2canvas: { scale: 1.6, useCORS: true, backgroundColor: '#ffffff', logging: false },
                            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait', compress: true },
                            pagebreak: { mode: ['css', 'legacy'], avoid: ['tr', '.figures', '.dept-head', 'h2.sec', '.signatures', '.letterhead', '.title-block'] }
                        })
                        .from(copy)
                        .toPdf()
                        .get('pdf')
                        .then(function (pdf) {
                            var pages = pdf.internal.getNumberOfPages();
                            for (var i = 1; i <= pages; i++) {
                                pdf.setPage(i);
                                pdf.setFontSize(8);
                                pdf.setTextColor(130);
                                pdf.text(i + ' / ' + pages, 105, 289, { align: 'center' });
                            }
                        })
                        .save();
                }).catch(function () {
                    // No internet access to load the PDF library: fall back to the browser's "Save as PDF".
                    overlay.classList.remove('show');
                    window.print();
                }).then(function () {
                    overlay.classList.remove('show');
                    btn.disabled = false;
                });
            }

            menu.querySelector('[data-pdf-action="save"]').addEventListener('click', savePdf);
            menu.querySelector('[data-pdf-action="print"]').addEventListener('click', function () {
                setOpen(false);
                ready().then(nextFrame).then(function () { window.print(); });
            });

            @if (! empty($autoPrint))
                // Opened from the "PDF" button of the report builder: download the file right away.
                window.addEventListener('load', function () { setTimeout(savePdf, 300); });
            @endif
        })();
    </script>
</body>
</html>
