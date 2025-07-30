<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceSetting;

class AttendanceSettingsController extends Controller
{
    public function edit()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.index'),
            route('manage.attendance-settings.edit') => __('manage.attendance_settings.headline'),
        ];
        
        $setting = AttendanceSetting::where('scope_type', 'global')->first();
        return view('manage.attendance-settings.edit', compact('setting', 'breadcrumbs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'late_arrival_tolerance' => 'required|integer|min:0',
            'early_leave_tolerance' => 'required|integer|min:0',
        ]);

        $setting = AttendanceSetting::firstOrCreate(
            ['scope_type' => 'global'],
            ['scope_id' => null]
        );
        $setting->update([
            'late_arrival_tolerance' => $request->late_arrival_tolerance,
            'early_leave_tolerance' => $request->early_leave_tolerance,
        ]);

        flash()->success('Settings updated successfully.');
        return redirect()->back();
    }
}
