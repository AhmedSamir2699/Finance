<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::create(['name' => 'تقنية المعلومات', 'description' => 'تقنية المعلومات']);
        Department::create(['name' => 'الإدارة العامة', 'description' => 'الإدارة العامة']);
        Department::create(['name' => 'الإدارة المالية', 'description' => 'الإدارة المالية']);
        Department::create(['name' => 'الموارد البشرية', 'description' => 'الموارد البشرية']);
        Department::create(['name' => 'العلاقات العامة', 'description' => 'العلاقات العامة']);
        Department::create(['name' => 'التسويق', 'description' => 'التسويق']);
    }
}
