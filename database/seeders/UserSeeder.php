<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Limited set of authorized accounts.
     * There is no public registration — accounts are created here (or manually by the administration).
     */
    public function run(): void
    {
        $networksId = Department::where('slug', 'networks')->value('id');
        $securityId = Department::where('slug', 'security')->value('id');
        $developmentId = Department::where('slug', 'development')->value('id');



        $accounts = [
            [
                'username' => 'admin',
                'password' => 'admin123',
                'full_name' => 'مدير إدارة تقنية المعلومات',
                'role' => User::ROLE_ADMIN,
                'department_id' => null,
            ],
            [
                'username' => 'net_admin',
                'password' => 'net123',
                'full_name' => 'مهندس الشبكات',
                'role' => User::ROLE_STAFF,
                'department_id' => $networksId,
            ],

                 [
                'username' => 'prog_admin',
                'password' => 'prog123',
                'full_name' => 'مطور البرمجيات',
                'role' => User::ROLE_STAFF,
                'department_id' => $developmentId,
            ],
            [
                'username' => 'sec_analyst',
                'password' => 'sec123',
                'full_name' => 'محلل أمن المعلومات',
                'role' => User::ROLE_STAFF,
                'department_id' => $securityId,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['username' => $account['username']],
                [
                    'full_name' => $account['full_name'],
                    'role' => $account['role'],
                    'department_id' => $account['department_id'],
                    'password' => $account['password'],
                ]
            );
        }
    }
}
