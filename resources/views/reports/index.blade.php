@extends('layouts.app')

@section('title', 'التقارير')

@section('content')
    <section class="section">
        <div class="container report-wrap">
            <div class="report-intro">
                <h1>التقارير الدورية</h1>
                <p>أنشئ تقريراً رسمياً بأعمال قسم واحد أو جميع الأقسام، ثم اطبعه أو احفظه بصيغة PDF.</p>
            </div>

            <form method="GET" action="{{ route('reports.show') }}" target="_blank" class="report-card" id="report-form">
                {{-- Report type --}}
                <fieldset class="report-field">
                    <legend>نوع التقرير</legend>
                    <div class="period-options">
                        <label class="period-option">
                            <input type="radio" name="period" value="quarter" checked>
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 3v9h9"/></svg>
                                <b>ربع سنوي</b>
                                <small>3 أشهر</small>
                            </span>
                        </label>
                        <label class="period-option">
                            <input type="radio" name="period" value="half">
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 3v18"/><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor" fill-opacity=".15"/></svg>
                                <b>نصف سنوي</b>
                                <small>6 أشهر</small>
                            </span>
                        </label>
                        <label class="period-option">
                            <input type="radio" name="period" value="year">
                            <span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="currentColor" fill-opacity=".15"/></svg>
                                <b>سنوي</b>
                                <small>12 شهراً</small>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <div class="report-grid">
                    <div class="form-group">
                        <label for="year">السنة</label>
                        <select id="year" name="year" class="form-input">
                            @foreach ($years as $y)
                                <option value="{{ $y }}" @selected($y === $currentYear)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" data-period-field="quarter">
                        <label for="quarter">الربع</label>
                        <select id="quarter" name="quarter" class="form-input">
                            @foreach ($quarterNames as $n => $name)
                                <option value="{{ $n }}" @selected($n === $currentQuarter)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" data-period-field="half" hidden>
                        <label for="half">النصف</label>
                        <select id="half" name="half" class="form-input" disabled>
                            @foreach ($halfNames as $n => $name)
                                <option value="{{ $n }}" @selected($n === $currentHalf)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group report-dept">
                        <label for="department">القسم</label>
                        <select id="department" name="department" class="form-input">
                            <option value="all">جميع الأقسام</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->slug }}" @selected(! auth()->user()->isAdmin() && auth()->user()->department_id === $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <fieldset class="report-field report-options">
                    <legend>محتوى التقرير</legend>
                    <label class="form-check">
                        <input type="checkbox" name="team" value="1" checked>
                        <span>إضافة أسماء فريق العمل</span>
                    </label>
                    <label class="form-check">
                        <input type="checkbox" name="images" value="1">
                        <span>تضمين صور الأعمال</span>
                    </label>
                </fieldset>

                <div class="report-actions">
                    <button type="submit" class="btn btn-primary" formaction="{{ route('reports.show') }}" formtarget="_blank">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        عرض التقرير
                    </button>
                    <button type="submit" class="btn btn-outline btn-export" formaction="{{ route('reports.show') }}" formtarget="_blank" name="print" value="1">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M12 11v6M9 14l3 3 3-3"/></svg>
                        PDF
                    </button>
                    <button type="submit" class="btn btn-outline btn-export" formaction="{{ route('reports.word') }}" formtarget="_self">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="m8.5 12 1.2 5 1.8-4 1.8 4 1.2-5"/></svg>
                        Word
                    </button>
                </div>
                <p class="report-note">
                    PDF: ينزّل ملف PDF جاهز (وتقدر تطبعه من داخل التقرير). Word: ملف قابل للتعديل.
                </p>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
<style>
    .report-wrap {
        max-width: 760px;
    }

    .report-intro {
        margin-bottom: 22px;
    }

    .report-intro h1 {
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 800;
        color: var(--navy-800);
        margin-bottom: 4px;
    }

    .report-intro p {
        color: var(--text-soft);
    }

    .report-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        padding: 28px;
    }

    .report-field {
        border: none;
        margin-bottom: 22px;
    }

    .report-field legend {
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--navy-800);
        margin-bottom: 10px;
    }

    .period-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .period-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .period-option span {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        padding: 16px 10px;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-md);
        cursor: pointer;
        text-align: center;
        transition: border-color var(--t-fast), background var(--t-fast), box-shadow var(--t-fast);
    }

    .period-option svg {
        width: 28px;
        height: 28px;
        color: var(--muted);
        margin-bottom: 6px;
        transition: color var(--t-fast);
    }

    .period-option b {
        color: var(--navy-800);
        font-size: 0.95rem;
    }

    .period-option small {
        color: var(--muted);
        font-size: 0.78rem;
    }

    .period-option span:hover {
        border-color: var(--gold-400);
    }

    .period-option input:checked + span {
        border-color: var(--accent);
        background: rgba(204, 55, 59, 0.05);
        box-shadow: 0 0 0 3px rgba(204, 55, 59, 0.1);
    }

    .period-option input:checked + span svg {
        color: var(--accent);
    }

    .period-option input:focus-visible + span {
        outline: 2px solid var(--accent);
        outline-offset: 2px;
    }

    .report-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 28px;
        margin: 6px 0 0;
    }

    .report-options legend {
        width: 100%;
    }

    .report-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0 16px;
    }

    .report-dept {
        grid-column: 1 / -1;
    }

    .report-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .report-actions .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .report-actions .btn svg {
        width: 18px;
        height: 18px;
    }

    .btn-export {
        background: #fff;
        color: var(--navy-800);
        border: 1.5px solid var(--border);
    }

    .btn-export:hover {
        border-color: var(--gold-400);
        color: var(--gold-700);
    }

    .report-note {
        margin-top: 10px;
        font-size: 0.82rem;
        color: var(--muted);
    }

    @media (max-width: 560px) {
        .report-card {
            padding: 20px;
        }

        .period-options {
            gap: 8px;
        }

        .period-option span {
            padding: 12px 6px;
        }

        .report-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<script>
    (function () {
        var form = document.getElementById('report-form');
        if (!form) return;

        function sync() {
            var period = form.querySelector('input[name="period"]:checked').value;
            form.querySelectorAll('[data-period-field]').forEach(function (group) {
                var active = group.getAttribute('data-period-field') === period;
                group.hidden = !active;
                group.querySelectorAll('select').forEach(function (s) { s.disabled = !active; });
            });
        }

        form.querySelectorAll('input[name="period"]').forEach(function (r) {
            r.addEventListener('change', sync);
        });
        sync();
    })();
</script>
@endpush
