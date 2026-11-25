<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * The six departments of the IT Management Department.
     * Existing slugs are kept so current achievements and user accounts stay linked.
     * Adding another department later only needs an extra entry here.
     */
    public function run(): void
    {
        $departments = [
            [
                'slug' => 'development',
                'name' => 'قسم البرمجة والتطوير',
                'description' => 'تصميم وتطوير الأنظمة الداخلية والتطبيقات والبوابات الإلكترونية لمصلحة الضرائب.',
                'icon' => 'code',
                'color' => '#e07a2e',
                'sort_order' => 1,
            ],
            [
                'slug' => 'operations',
                'name' => 'قسم التشغيل والعمليات',
                'description' => 'تشغيل الخوادم ومراكز البيانات ومتابعة أداء الأنظمة على مدار الساعة، وإدارة النسخ الاحتياطي واستمرارية العمل.',
                'icon' => 'server',
                'color' => '#1e3a5f',
                'sort_order' => 2,
            ],
            [
                'slug' => 'e-services',
                'name' => 'قسم الخدمات الإلكترونية',
                'description' => 'إدارة وتطوير الخدمات الإلكترونية المقدمة للمكلفين والمواطنين، وتسهيل الوصول إلى خدمات المصلحة عبر الإنترنت.',
                'icon' => 'globe',
                'color' => '#2a8c82',
                'sort_order' => 3,
            ],
            [
                'slug' => 'networks',
                'name' => 'قسم الشبكات والدعم الفني',
                'description' => 'تصميم وربط وصيانة شبكات المصلحة الداخلية والخارجية، وتقديم الدعم الفني للموظفين وضمان الاتصال المستمر بين جميع الفروع.',
                'icon' => 'network',
                'color' => '#b8925a',
                'sort_order' => 4,
            ],
            [
                'slug' => 'documentation',
                'name' => 'قسم التوثيق والمعلومات',
                'description' => 'أرشفة الوثائق والمعلومات وتنظيم قواعد البيانات وضمان دقة البيانات وسلامتها في المصلحة.',
                'icon' => 'document',
                'color' => '#6d5a9c',
                'sort_order' => 5,
            ],
            [
                'slug' => 'security',
                'name' => 'قسم أمن المعلومات',
                'description' => 'حماية الأنظمة والبيانات من التهديدات السيبرانية وتطبيق سياسات الأمن السيبراني في المصلحة.',
                'icon' => 'shield',
                'color' => '#c7363f',
                'sort_order' => 6,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['slug' => $department['slug']], $department);
        }
    }
}
