<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttendanceSetting;

class AttendanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttendanceSetting::create([
            'scope_type' => 'global',
            'scope_id' => null,
            'late_arrival_tolerance' => 10,
            'early_leave_tolerance' => 5,
        ]);
    }
}
