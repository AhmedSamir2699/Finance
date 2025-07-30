<?php

namespace Database\Seeders;

use App\Models\User;
use Flasher\Laravel\Http\Request;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobPositions = [
            "مهندس برمجيات",
            "مدير مشروع",
            "أخصائي تسويق",
            "محلل بيانات",
            "مصمم جرافيك",
            "مدير الموارد البشرية",
            "مدير منتج",
            "تنفيذي مبيعات",
            "ممثل دعم العملاء",
            "محلل مالي",
            "منسق عمليات",
            "كاتب محتوى",
            "مساعد تطوير الأعمال",
            "مستشار تكنولوجيا المعلومات",
            "مصمم تجربة المستخدم وواجهة المستخدم",
            "مختبر ضمان الجودة",
            "مدير سلسلة التوريد",
            "مساعد إداري",
            "مدير وسائل التواصل الاجتماعي",
            "مستشار قانوني"
        ];

        $roles = Role::whereNotIn('name', ['super-admin', 'ceo'])->get();


        $user = User::create([
            'name' => 'عبدالعزيز سعد الشهري',
            'email' => 'me@abdulaziz-d.com',
            'password' => bcrypt('12345678'),
            'department_id' => 1,
            'phone' => '0568052503',
            'position' => 'مدير النظام',
        ]);


        $user->assignRole('super-admin');

        $user->sendNotification('تم انشاء حسابك بنجاح بصلاحية ' . $user->roles()->first()->display_name);

        if (env('APP_ENV') == 'local') {

            for ($i = 0; $i < 10; $i++) {
                $user = User::create([
                    'name' => fake('ar_SA')->firstName() . ' ' . fake('ar_SA')->lastName(),
                    'email' => fake()->unique()->safeEmail(),
                    'phone' => '0' . fake()->numberBetween('500000000', '599999999'),
                    'password' => bcrypt('12345678'),
                    'department_id' => rand(2, 6),
                    'position' => $jobPositions[rand(0, count($jobPositions) - 1)],
                ]);

                $user->assignRole($roles->random()->name);
            }

            $user = User::create([
                'name' => 'محمد عبدالله',
                'email' => fake()->unique()->safeEmail(),
                'password' => bcrypt('12345678'),
                'department_id' => 1,
                'phone' => '0' . fake()->numberBetween('500000000', '599999999'),
                'position' => 'رئيس القسم',
            ]);

            $user->assignRole('ceo');


            \App\Models\RequestFormCategory::create([
                'name' => 'الموارد البشرية'
            ]);
        }
    }
}
