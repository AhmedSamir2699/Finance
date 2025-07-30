<?php

namespace Database\Seeders;

use App\Models\ExecutivePlanColumn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExecutivePlanColumnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //كلـــــــمة في مسجد	درس        في مسجد	دورة        في مسجد	محاضرة عن بعد	كلـــــــمة عن بعد	دورة       عن بعد	تغريدة ورسالة نصية واتس	تصميم	فيديو دعوي	فيديو توعوي ديني	فيديو تعليمي ديني	فيديو فقهي	فيلم دعوي	ملف pdf	مسابقة الكترونية	رابط صفحة موقع
        ExecutivePlanColumn::create([
            'name' => 'كلمة في مسجد',
            'order' => 0,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'درس في مسجد',
            'order' => 1,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'دورة في مسجد',
            'order' => 2,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'محاضرة عن بعد',
            'order' => 3,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'كلمة عن بعد',
            'order' => 4,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'دورة عن بعد',
            'order' => 5,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'تغريدة ورسالة نصية واتس',
            'order' => 6,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'تصميم',
            'order' => 7,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'فيديو دعوي',
            'order' => 8,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'فيديو توعوي ديني',
            'order' => 9,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'فيديو تعليمي ديني',
            'order' => 10,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'فيديو فقهي',
            'order' => 11,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'فيلم دعوي',
            'order' => 12,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'ملف pdf',
            'order' => 13,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'مسابقة الكترونية',
            'order' => 14,
            'department_id' => 1
        ]);
        ExecutivePlanColumn::create([
            'name' => 'رابط صفحة موقع',
            'order' => 15,
            'department_id' => 1
        ]);
    }
}
