<?php

namespace App\Livewire\Manage\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Permission;

class PermissionsEditor extends Component
{
    public $role;
    public $categories;

    public function mount($role)
    {
        $this->role = $role;
        $this->categories = [
            'users' => [
                'name' => 'المستخدمين',
                'permissions' => Permission::where('name', 'like', 'user.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ],
            'departments' => [
                'name' => 'الأقسام',
                'permissions' => Permission::where('name', 'like', 'department.%')->OrderBy('id')->get(),
                'isExpanded' => false,

            ],
            'timesheets' => [
                'name' => 'الحضور والانصراف',
                'permissions' => Permission::where('name', 'like', 'timesheet.%')->OrderBy('id')->get(),
                'isExpanded' => false,

            ],
            'messages' => [
                'name' => 'الرسائل',
                'permissions' => Permission::where('name', 'like', 'message.%')->OrderBy('id')->get(),
                'isExpanded' => false,

            ],
            'tasks' => [
                'name' => 'المهام',
                'permissions' => Permission::where('name', 'like', 'task.%')->OrderBy('id')->get(),
                'isExpanded' => false,

            ],
            'elections' => [
                'name' => 'ادارة الانتخابات',
                'permissions' => Permission::where('name', 'like', 'elections.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ],
            'roles' => [
                'name' => 'الأدوار',
                'permissions' => Permission::where('name', 'like', 'role.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ],
            'requests' => [
                'name' => 'الطلبات',
                'permissions' => Permission::where('name', 'like', 'request.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ],
            'executive-plan' => [
                'name' => 'الخطة التنفيذية',
                'permissions' => Permission::where('name', 'like', 'executive-plan.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ],
            'operational-plan' => [
                'name' => 'الخطة التشغيلية',
                'permissions' => Permission::where('name', 'like', 'operational_plan.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ],
            'evaluate' => [
                'name' => 'تقييم الأداء',
                'permissions' => Permission::where('name', 'like', 'evaluate.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ],

            'reports' => [
                'name' => 'التقارير',
                'permissions' => Permission::where('name', 'like', 'report.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ],
            'manage' => [
                'name' => 'الإدارة',
                'permissions' => Permission::where('name', 'like', 'manage.%')->OrderBy('id')->get(),
                'isExpanded' => false,
            ]
        ];
    }

    public function toggleCategory($category)
    {
        $this->categories[$category]['isExpanded'] = !$this->categories[$category]['isExpanded'];
        $this->categories = collect($this->categories)->toArray();
    }

    public function togglePermission($permission)
    {
        if (auth()->user()->can('role.assign-permission')) {
            if ($this->role->permissions->contains($permission)) {
                $this->role->permissions()->detach($permission);
            } else {
                $this->role->givePermissionTo($permission);
            }
        }
    }

    public function render()
    {
        return view('livewire.manage.roles.permissions-editor');
    }
}
