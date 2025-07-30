<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Election;
use App\Models\RequestForm;
use App\Models\Role;
use App\Models\AttendanceSetting;
use App\Models\SidebarLink;
use App\Models\Task;
use App\Models\Timesheet;
use App\Models\Setting;
use Spatie\Permission\Models\Permission;

class SettingsController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.settings'),
        ];

        return view('manage.settings.index', compact('breadcrumbs'));
    }

    public function edit()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.settings'),
            route('manage.settings.edit') => __('settings.edit_settings'),
        ];

        return view('manage.settings.edit', compact('breadcrumbs'));
    }
} 