<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Placeholder achievements used for development and visual testing.
     * Replace these with real Authority data before going live.
     */
    public function run(): void
    {
        $data = [
            [
                'department' => 'networks',
                'title' => 'ترقية البنية التحتية للشبكة الأساسية',
                'description' => 'أعيد بناء الشبكة الأساسية للمصلحة وترقية أجهزة التوجيه والمبدلات، ما خفّض معدل انقطاع الخدمة إلى النصف. تم ربط جميع الأقسام الرئيسية والفروع بالألياف البصرية وتحديث مراكز البيانات.',
                'achievement_at' => '-3 months',
                'employees' => ['م. أحمد السويعي', 'م. فاطمة الزنتاني', 'م. محمد بن عاشور'],
            ],
            [
                'department' => 'networks',
                'title' => 'تغطية شبكة لاسلكية آمنة للمبنى بأكمله',
                'description' => 'تمديد التغطية اللاسلكية لجميع طوابق المبنى الرئيسي مع نظام موحّد لتوثيق الأجهزة، وفصل شبكة الموظفين عن شبكة الضيوف.',
                'achievement_at' => '-11 months',
                'employees' => ['م. أحمد السويعي', 'م. سارة الورفلي'],
            ],
            [
                'department' => 'documentation',
                'title' => 'برنامج رقمنة الوثائق',
                'description' => 'تحويل أكثر من 50,000 وثيقة ورقية إلى سجلات رقمية ممسوحة ضوئياً ضمن نظام أرشفة مركزي مفهرس بذكاء، مما سرّع البحث والاسترجاع.',
                'achievement_at' => '-1 month',
                'employees' => ['خالد الدوكالي', 'منى القاضي'],
            ],
            [
                'department' => 'security',
                'title' => 'شهادة ISO 27001 للأمن السيبراني',
                'description' => 'قيادة تنفيذ معايير أمن المعلومات وإدارة المخاطر مع جهة منح شهادات دولية، لتحصل المصلحة على شهادة ISO/IEC 27001 لأول مرة.',
                'achievement_at' => '-6 months',
                'employees' => ['م. هدى الصديق', 'م. عمر الشريف', 'م. آمال بن عمر'],
            ],
            [
                'department' => 'development',
                'title' => 'إطلاق البوابة الإلكترونية للمكلفين',
                'description' => 'تطوير وإطلاق البوابة الإلكترونية التي تمكّن المكلفين من الاطلاع على ملفاتهم وتقديم الإقرارات إلكترونياً، مع تسجيل أكثر من 10,000 عملية دفع إلكتروني خلال الأشهر الستة الأولى.',
                'achievement_at' => '-2 months',
                'employees' => ['م. يوسف المصراتي', 'م. إيمان الترهوني', 'م. عبدالله صالح'],
            ],
            [
                'department' => 'development',
                'title' => 'نظام إدارة الموارد البشرية الداخلي',
                'description' => 'بناء نظام داخلي متكامل لإدارة الموارد البشرية يغطي الحضور والإجازات والرواتب، ومربوط بالأنظمة المالية القائمة.',
                'achievement_at' => '-14 months',
                'employees' => ['م. يوسف المصراتي', 'م. إيمان الترهوني'],
            ],
        ];

        $departmentBySlug = Department::pluck('id', 'slug');

        foreach ($data as $item) {
            $achievement = Achievement::create([
                'department_id' => $departmentBySlug[$item['department']],
                'title' => $item['title'],
                'description' => $item['description'],
                'achievement_at' => Carbon::parse($item['achievement_at']),
            ]);

            foreach ($item['employees'] as $name) {
                $employee = Employee::firstOrCreate(['full_name' => $name]);
                $achievement->employees()->attach($employee->id);
            }
        }

        // No media on placeholders — thumbnails fall back to the department icon.
    }
}
