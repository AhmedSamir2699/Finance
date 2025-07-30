<?php

namespace Database\Seeders;

use App\Models\SidebarLink;
use Illuminate\Database\Seeder;

class SidebarLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing sidebar links
        SidebarLink::truncate();

        // Create main navigation links
        $dashboard = SidebarLink::create([
            'title' => 'Dashboard',
            'url' => 'dashboard',
            'icon' => 'fa fa-tachometer-alt',
            'permission' => null,
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 0,
        ]);

        $tasks = SidebarLink::create([
            'title' => 'Tasks',
            'url' => 'tasks.index',
            'icon' => 'fa fa-tasks',
            'permission' => 'task.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 1,
        ]);

        $executivePlan = SidebarLink::create([
            'title' => 'Executive Plan',
            'url' => 'executive-plan.index',
            'icon' => 'fa fa-chart-line',
            'permission' => 'executive-plan.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 2,
        ]);

        $departments = SidebarLink::create([
            'title' => 'Departments',
            'url' => 'departments.index',
            'icon' => 'fa fa-building',
            'permission' => 'department.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 3,
        ]);

        $users = SidebarLink::create([
            'title' => 'Users',
            'url' => 'users.index',
            'icon' => 'fa fa-users',
            'permission' => 'user.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 4,
        ]);

        $timesheets = SidebarLink::create([
            'title' => 'Timesheets',
            'url' => 'timesheets.index',
            'icon' => 'fa fa-clock',
            'permission' => 'timesheet.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 5,
        ]);

        $messages = SidebarLink::create([
            'title' => 'Messages',
            'url' => 'messages.index',
            'icon' => 'fa fa-envelope',
            'permission' => 'message.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 6,
        ]);

        $roles = SidebarLink::create([
            'title' => 'Roles & Permissions',
            'url' => 'manage.roles.index',
            'icon' => 'fa fa-user-shield',
            'permission' => 'role.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 7,
        ]);

        $sidebarLinks = SidebarLink::create([
            'title' => 'Sidebar Links',
            'url' => 'manage.sidebar-links.index',
            'icon' => 'fa fa-link',
            'permission' => 'sidebar-link.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 8,
        ]);

        $staticPages = SidebarLink::create([
            'title' => 'Static Pages',
            'url' => 'manage.static-pages.index',
            'icon' => 'fa fa-file-alt',
            'permission' => 'manage.settings',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'order' => 9,
        ]);

        // Create child links for Tasks
        SidebarLink::create([
            'title' => 'My Tasks',
            'url' => 'tasks.my-tasks',
            'icon' => 'fa fa-user-check',
            'permission' => 'task.view-self',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'parent_id' => $tasks->id,
            'order' => 0,
        ]);

        SidebarLink::create([
            'title' => 'Create Task',
            'url' => 'tasks.create',
            'icon' => 'fa fa-plus',
            'permission' => 'task.create',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'parent_id' => $tasks->id,
            'order' => 1,
        ]);

        // Create child links for Executive Plan
        SidebarLink::create([
            'title' => 'View Plan',
            'url' => 'executive-plan.index',
            'icon' => 'fa fa-eye',
            'permission' => 'executive-plan.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'parent_id' => $executivePlan->id,
            'order' => 0,
        ]);

        SidebarLink::create([
            'title' => 'Edit Plan',
            'url' => 'executive-plan.edit',
            'icon' => 'fa fa-edit',
            'permission' => 'executive-plan.edit-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'parent_id' => $executivePlan->id,
            'order' => 1,
        ]);

        // Create child links for Users
        SidebarLink::create([
            'title' => 'All Users',
            'url' => 'manage.users.index',
            'icon' => 'fa fa-users',
            'permission' => 'user.view-any',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'parent_id' => $users->id,
            'order' => 0,
        ]);

        SidebarLink::create([
            'title' => 'User Reports',
            'url' => 'users.reports',
            'icon' => 'fa fa-chart-bar',
            'permission' => 'user.view-reports',
            'visibility' => 'authenticated',
            'is_external' => false,
            'is_active' => true,
            'parent_id' => $users->id,
            'order' => 1,
        ]);
    }
} 