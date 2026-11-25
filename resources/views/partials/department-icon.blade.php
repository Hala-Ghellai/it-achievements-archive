{{--
    Department icon paths (24×24 grid, stroke icons).
    Usage, inside an <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"> element:
        @include('partials.department-icon', ['icon' => $department->icon])
--}}
@switch($icon)
    {{-- البرمجة والتطوير: نافذة محرر برمجي --}}
    @case('code')
        <rect x="2.5" y="4" width="19" height="16" rx="2.5"/><path d="M2.5 8.5h19"/><path d="M5.6 6.25h.01M8.1 6.25h.01"/><path d="M9.5 12.25 7.25 14.5l2.25 2.25M14.5 12.25l2.25 2.25-2.25 2.25"/>
        @break

    {{-- التشغيل والعمليات: خوادم مترابطة (مركز البيانات) --}}
    @case('server')
        <rect x="3" y="3" width="18" height="7.5" rx="2"/><rect x="3" y="13.5" width="18" height="7.5" rx="2"/><path d="M6.75 6.75h.01M6.75 17.25h.01"/><path d="M10.5 6.75h6.75M10.5 17.25h6.75"/><path d="M12 10.5v3"/>
        @break

    {{-- الخدمات الإلكترونية: خدمات عبر الإنترنت مع مؤشر النقر --}}
    @case('globe')
        <path d="M21 12a9 9 0 1 0-9 9"/><path d="M3.6 9h16.8M3.6 15h7.9"/><path d="M12 3a15 15 0 0 1 3.9 9.5M12 3a15 15 0 0 0 0 18"/><path d="m14.5 14.5 6.5 2.4-2.9 1.1-1.1 2.9z"/>
        @break

    {{-- الشبكات والدعم الفني: جهاز توجيه مع إشارة اتصال --}}
    @case('network')
        <rect x="2.5" y="13.5" width="19" height="7" rx="2"/><path d="M6.25 17h.01M9.5 17h.01M14 17h3.75"/><path d="M12 13.5v-3"/><path d="M9.5 8a3.5 3.5 0 0 1 5 0"/><path d="M7 5.5a7 7 0 0 1 10 0"/>
        @break

    {{-- التوثيق والمعلومات: صندوق أرشيف --}}
    @case('document')
        <rect x="2.5" y="3.5" width="19" height="5" rx="1.5"/><path d="M4.5 8.5v10a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-10"/><path d="M10 12.5h4"/>
        @break

    {{-- أمن المعلومات: درع مع قفل --}}
    @case('shield')
        <path d="M12 2.5 4 5.5v6c0 5 3.4 8.8 8 10 4.6-1.2 8-5 8-10v-6z"/><rect x="8.75" y="11" width="6.5" height="5.25" rx="1.2"/><path d="M10.25 11V9.6a1.75 1.75 0 0 1 3.5 0V11"/>
        @break

    @default
        <rect x="3" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="3" width="7.5" height="7.5" rx="1.5"/><rect x="3" y="13.5" width="7.5" height="7.5" rx="1.5"/><rect x="13.5" y="13.5" width="7.5" height="7.5" rx="1.5"/>
@endswitch
