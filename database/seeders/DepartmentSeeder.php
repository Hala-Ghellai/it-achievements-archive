<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * The four departments of the IT Management Department.
     * A future 5th department only needs an extra entry here.
     */
    public function run(): void
    {
        $departments = [
            [
                'slug' => 'networks',
                'name' => 'قسم الشبكات',
                'description' => 'تصميم وربط وصيانة شبكات المصلحة الداخلية والخارجية، وضمان الاتصال المستمر بين جميع الفروع.',
                'icon' => 'network',
                'color' => '#b8925a',
                'sort_order' => 1,
            ],
            [
                'slug' => 'documentation',
                'name' => 'قسم التوثيق والمعلومات',
                'description' => 'أرشفة الوثائق والمعلومات وتنظيم قواعد البيانات وضمان دقة البيانات وسلامتها في المصلحة.',
                'icon' => 'document',
                'color' => '#d8b27a',
                'sort_order' => 2,
            ],
            [
                'slug' => 'security',
                'name' => 'قسم أمن المعلومات',
                'description' => 'حماية الأنظمة والبيانات من التهديدات السيبرانية وتطبيق سياسات الأمن السيبراني في المصلحة.',
                'icon' => 'shield',
                'color' => '#c7363f',
                'sort_order' => 3,
            ],
            [
                'slug' => 'development',
                'name' => 'قسم البرمجة والتطوير',
                'description' => 'تصميم وتطوير الأنظمة الداخلية والتطبيقات والبوابات الإلكترونية لمصلحة الضرائب.',
                'icon' => 'code',
                'color' => '#f28c38',
                'sort_order' => 4,
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(['slug' => $department['slug']], $department);
        }
    }
}
