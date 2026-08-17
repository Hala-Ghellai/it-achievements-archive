@php
    $navDepartments = \App\Models\Department::orderBy('sort_order')->get();
@endphp

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col footer-col-brand">
                <div class="footer-logo-row">
                    <img src="{{ asset('images/mashlaha-logo.jpg') }}" alt="شعار {{ config('site.organization_name') }}" class="footer-logo">
                    <div>
                        <div class="footer-brand-name">{{ config('site.department_name') }}</div>
                        <div class="footer-org">{{ config('site.organization_name') }}</div>
                    </div>
                </div>
                <p class="footer-about">
                    أرشيف يوثّق إنجازات فرق إدارة تقنية المعلومات في المصلحة — رحلة التحول الرقمي في مكان واحد.
                </p>
            </div>

            <div class="footer-col footer-col-quick">
                <h4 class="footer-heading">روابط سريعة</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">الرئيسية</a></li>
                    @foreach ($navDepartments as $department)
                        <li><a href="{{ route('departments.show', $department->slug) }}">{{ $department->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-col footer-col-contact">
                <h4 class="footer-heading">تواصل معنا</h4>
                <ul class="footer-links footer-contact">
                    <li>
                        <a href="mailto:{{ config('site.contact_email') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/></svg>
                            {{ config('site.contact_email') }}
                        </a>
                    </li>
                    <li>
                        <a href="tel:{{ config('site.contact_phone') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.81.32 1.6.6 2.36a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.72-1.17a2 2 0 0 1 2.11-.45c.76.28 1.55.48 2.36.6A2 2 0 0 1 22 16.92z"/></svg>
                            {{ config('site.contact_phone') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="footer-copy" data-year data-founding="{{ config('site.founding_year') }}" data-org="{{ config('site.organization_name') }}"></div>
        </div>
    </div>
</footer>

@push('scripts')
<style>
    .footer {
        background: linear-gradient(180deg, #101828 0%, #0b1019 100%);
        padding: 0;
        margin-top: 72px;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 40px;
        padding: 56px 0 44px;
    }

    .footer-logo-row {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .footer-logo {
        width: 56px;
        height: 56px;
        object-fit: contain;
        background: #fff;
        border-radius: 14px;
        padding: 5px;
        box-shadow: var(--shadow-md);
    }

    .footer-brand-name {
        color: #fff;
        font-weight: 800;
        font-size: 1.05rem;
    }

    .footer-org {
        color: var(--gold-300);
        font-size: 0.85rem;
    }

    .footer-about {
        margin-top: 16px;
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.9rem;
        line-height: 1.8;
        max-width: 320px;
    }

    .footer-heading {
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .footer-links {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 0;
        padding: 0;
    }

    .footer-links a {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
        transition: color var(--t-fast);
    }

    .footer-links a:hover {
        color: var(--gold-300);
    }

    .footer-contact a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .footer-contact svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .footer-copy {
        border-top: none;
        padding-block: 18px;
        text-align: center;
    }

    @media (max-width: 860px) {
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 18px;
            padding: 30px 0 22px;
        }

        .footer-col-quick {
            display: none;
        }

        .footer-col-brand {
            text-align: center;
        }

        .footer-logo-row {
            justify-content: center;
            gap: 10px;
        }

        .footer-logo {
            width: 42px;
            height: 42px;
        }

        .footer-brand-name {
            font-size: 0.95rem;
        }

        .footer-about {
            display: none;
        }

        .footer-col-contact {
            text-align: center;
        }

        .footer-heading {
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .footer-contact {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px 22px;
        }

        .footer-links a {
            font-size: 0.85rem;
        }
    }
</style>
@endpush