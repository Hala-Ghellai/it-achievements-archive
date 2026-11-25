<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site configuration
    |--------------------------------------------------------------------------
    |
    | Editable values used across the website (hero, footer, stats, etc.).
    | Update these values in one place instead of hardcoding them in views.
    |
    */

    'organization_name' => env('SITE_ORGANIZATION', 'مصلحة الضرائب'),

    'department_name' => env('SITE_DEPARTMENT', 'إدارة تقنية المعلومات'),

    'founding_year' => (int) env('SITE_FOUNDING_YEAR', 2010),

    'contact_email' => env('SITE_EMAIL', 'it@tax.gov.ly'),

    'contact_phone' => env('SITE_PHONE', '+218 21 000 0000'),

    /*
    |--------------------------------------------------------------------------
    | Official report header & signatures
    |--------------------------------------------------------------------------
    */

    'report' => [
        'country' => env('REPORT_COUNTRY', 'دولة ليبيا'),
        'ministry' => env('REPORT_MINISTRY', 'وزارة المالية'),
        'prepared_by_title' => env('REPORT_PREPARED_BY', 'معدّ التقرير'),
        'prepared_by_name' => env('REPORT_PREPARED_BY_NAME', 'قسم التوثيق والمعلومات'),
        'approved_by_title' => env('REPORT_APPROVED_BY', 'مدير إدارة تقنية المعلومات'),
    ],
];
