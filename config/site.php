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

    'organization_name' => env('SITE_ORGANIZATION', 'مصلحة الضرائب الليبية'),

    'department_name' => env('SITE_DEPARTMENT', 'إدارة تقنية المعلومات'),

    'founding_year' => (int) env('SITE_FOUNDING_YEAR', 2010),

    'contact_email' => env('SITE_EMAIL', 'it@tax.gov.ly'),

    'contact_phone' => env('SITE_PHONE', '+218 21 000 0000'),
];
