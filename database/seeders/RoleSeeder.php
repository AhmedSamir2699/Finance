<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\RoleRelationship;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Role::updateOrCreate([
            'name' => 'super-admin',
            'display_name' => 'مدير النظام'
        ]);


        Role::updateOrCreate([
            'name' => 'ceo',
            'display_name' => 'الرئيس التنفيذي'
        ]);
        Role::updateOrCreate([
            'display_name' => 'الأمين العام',
            'name' => 'secretary-general'
        ]);
        Role::updateOrCreate([
            'display_name' => 'المدير التنفيذي',
            'name' => 'executive-director'
        ]);

        Role::updateOrCreate([
            'name' => 'department-head',
            'display_name' => 'رئيس القسم'
        ]);
        Role::updateOrCreate([
            'name' => 'employee',
            'display_name' => 'موظف'
        ]);


        // User Permissions
        Permission::updateOrCreate(
            ['name' => 'user.impersonate', 'guard_name' => 'web'],
            ['display_name' => 'التنقل كمستخدم آخر']
        );

        Permission::updateOrCreate(['name' => 'user.create', 'display_name' => 'إنشاء مستخدم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'user.edit', 'display_name' => 'تعديل مستخدم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'user.delete', 'display_name' => 'حذف مستخدم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'user.view', 'display_name' => 'عرض مستخدم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'user.index', 'display_name' => 'عرض جميع المستخدمين', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'user.assign-role', 'display_name' => 'تعيين دور للمستخدم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'user.remove-role', 'display_name' => 'إزالة دور من المستخدم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'user.assign-department', 'display_name' => 'تعيين قسم للمستخدم', 'guard_name' => 'web']);

        // Department Permissions
        Permission::updateOrCreate(['name' => 'department.view-self', 'display_name' => 'عرض القسم الخاص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'department.view-any', 'display_name' => 'عرض الأقسام لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'department.view-subordinates', 'display_name' => 'عرض الأقسام للمرؤوسين', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'department.view-members', 'display_name' => 'عرض أعضاء القسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'department.create', 'display_name' => 'إنشاء قسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'department.edit', 'display_name' => 'تعديل قسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'department.delete', 'display_name' => 'حذف قسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'department.index', 'display_name' => 'عرض جميع الأقسام', 'guard_name' => 'web']);

        // Role Permissions
        Permission::updateOrCreate(['name' => 'role.create', 'display_name' => 'إنشاء دور', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'role.edit', 'display_name' => 'تعديل دور', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'role.delete', 'display_name' => 'حذف دور', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'role.view', 'display_name' => 'عرض دور', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'role.index', 'display_name' => 'عرض جميع الأدوار', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'role.assign-permission', 'display_name' => 'تعيين صلاحية للدور', 'guard_name' => 'web']);

        // Message Permissions
        Permission::updateOrCreate(['name' => 'message.create', 'display_name' => 'إنشاء رسالة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'message.delete', 'display_name' => 'حذف رسالة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'message.view', 'display_name' => 'عرض رسالة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'message.index', 'display_name' => 'عرض جميع الرسائل', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'message.reply', 'display_name' => 'الرد على الرسالة', 'guard_name' => 'web']);

        // Request Permissions
        Permission::updateOrCreate(['name' => 'request.create', 'display_name' => 'إنشاء طلب', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'request.edit', 'display_name' => 'تعديل طلب', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'request.delete', 'display_name' => 'حذف طلب', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'request.view', 'display_name' => 'عرض طلب', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'request.view-any', 'display_name' => 'عرض طلب لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'request.index', 'display_name' => 'عرض جميع الطلبات', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'request.approve', 'display_name' => 'إعتماد الطلب', 'guard_name' => 'web']);

        // Task Permissions
        Permission::updateOrCreate(['name' => 'task.create', 'display_name' => 'إنشاء مهمة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.edit', 'display_name' => 'تعديل مهمة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.delete', 'display_name' => 'حذف مهمة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.view', 'display_name' => 'عرض مهمة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.index', 'display_name' => 'عرض جميع المهام', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.assign', 'display_name' => 'تعيين مهمة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.view-any', 'display_name' => 'عرض المهام لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.edit-any', 'display_name' => 'تعديل المهام لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.create-any', 'display_name' => 'إنشاء مهمة لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.delete-any', 'display_name' => 'حذف المهام لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.view-department', 'display_name' => 'عرض المهام للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.edit-department', 'display_name' => 'تعديل المهام للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.create-department', 'display_name' => 'إنشاء مهمة للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.delete-department', 'display_name' => 'حذف المهام للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.index-department', 'display_name' => 'عرض المهام للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.approve-department', 'display_name' => 'إعتماد المهمة للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.reject-department', 'display_name' => 'رفض المهمة للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.comment-department', 'display_name' => 'التعليق على المهمة للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.view-subordinates', 'display_name' => 'عرض المهام للمرؤوسين', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.approve-subordinates', 'display_name' => 'إعتماد المهمة للمرؤوسين', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.comment', 'display_name' => 'التعليق على المهمة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'task.view-history', 'display_name' => 'عرض تاريخ المهمة', 'guard_name' => 'web']);

        // Executive Plan Permissions
        Permission::updateOrCreate(['name' => 'executive-plan.view-self', 'display_name' => 'عرض الخطة التنفيذية الخاصة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.edit-self', 'display_name' => 'تعديل الخطة التنفيذية الخاصة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.export-self', 'display_name' => 'تصدير الخطة التنفيذية الخاصة', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.view-any', 'display_name' => 'عرض الخطة التنفيذية لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.edit-any', 'display_name' => 'تعديل الخطة التنفيذية لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.export-any', 'display_name' => 'تصدير الخطة التنفيذية لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.view-department', 'display_name' => 'عرض الخطة التنفيذية للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.edit-department', 'display_name' => 'تعديل الخطة التنفيذية للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.export-department', 'display_name' => 'تصدير الخطة التنفيذية للقسم', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.view-subordinates', 'display_name' => 'عرض الخطة التنفيذية للمرؤوسين', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.edit-subordinates', 'display_name' => 'تعديل الخطة التنفيذية للمرؤوسين', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'executive-plan.export-subordinates', 'display_name' => 'تصدير الخطة التنفيذية للمرؤوسين', 'guard_name' => 'web']);


        // Management Permissions
        Permission::updateOrCreate(['name' => 'manage.executive-plan', 'display_name' => 'إدارة الخطة التنفيذية', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage.departments', 'display_name' => 'إدارة الأقسام', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage.users', 'display_name' => 'إدارة المستخدمين', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage.roles', 'display_name' => 'إدارة الأدوار', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage.permissions', 'display_name' => 'إدارة الصلاحيات', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage.forms', 'display_name' => 'إدارة النماذج', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage.logs', 'display_name' => 'إدارة السجلات', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'manage.settings', 'display_name' => 'إدارة الإعدادات', 'guard_name' => 'web']);
        

        // Evaluation Permissions
        Permission::updateOrCreate(['name' => 'evaluate', 'display_name' => 'تقييم الأداء', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'evaluate.view', 'display_name' => 'عرض تقييم الأداء', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'evaluate.create', 'display_name' => 'إنشاء تقييم الأداء', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'evaluate.edit', 'display_name' => 'تعديل تقييم الأداء', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'evaluate.delete', 'display_name' => 'حذف تقييم الأداء', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'evaluate.index', 'display_name' => 'عرض جميع تقييمات الأداء', 'guard_name' => 'web']);

        
        // Daily Evaluation Report Permissions
        Permission::updateOrCreate(['name' => 'daily-evaluation-report.view', 'display_name' => 'عرض تقرير التقييم اليومي', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'daily-evaluation-report.export', 'display_name' => 'تصدير تقرير التقييم اليومي', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'daily-evaluation-report.index', 'display_name' => 'عرض جميع تقارير التقييم اليومي', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'daily-evaluation-report.view-any', 'display_name' => 'عرض تقرير التقييم اليومي لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'daily-evaluation-report.export-any', 'display_name' => 'تصدير تقرير التقييم اليومي لأي شخص', 'guard_name' => 'web']);

        // Monthly Evaluation Report Permissions
        Permission::updateOrCreate(['name' => 'monthly-evaluation-report.view', 'display_name' => 'عرض تقرير التقييم الشهري', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'monthly-evaluation-report.export', 'display_name' => 'تصدير تقرير التقييم الشهري', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'monthly-evaluation-report.index', 'display_name' => 'عرض جميع تقارير التقييم الشهري', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'monthly-evaluation-report.view-any', 'display_name' => 'عرض تقرير التقييم الشهري لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'monthly-evaluation-report.export-any', 'display_name' => 'تصدير تقرير التقييم الشهري لأي شخص', 'guard_name' => 'web']);

        // Yearly Evaluation Report Permissions
        Permission::updateOrCreate(['name' => 'yearly-evaluation-report.view', 'display_name' => 'عرض تقرير التقييم السنوي', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'yearly-evaluation-report.export', 'display_name' => 'تصدير تقرير التقييم السنوي', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'yearly-evaluation-report.index', 'display_name' => 'عرض جميع تقارير التقييم السنوي', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'yearly-evaluation-report.view-any', 'display_name' => 'عرض تقرير التقييم السنوي لأي شخص', 'guard_name' => 'web']);
        Permission::updateOrCreate(['name' => 'yearly-evaluation-report.export-any', 'display_name' => 'تصدير تقرير التقييم السنوي لأي شخص', 'guard_name' => 'web']);

        // Summary Report Permissions
        Permission::updateOrCreate(['name' => 'summary.view', 'display_name' => 'عرض التقرير الملخص', 'guard_name' => 'web']);


        // Expenese
        Permission::updateOrCreate(['name' => 'expense_access', 'display_name' => 'الدخول الي المصروفات', 'guard_name' => 'web']);



        Role::findByName('super-admin')->givePermissionTo(Permission::all());

        RoleRelationship::insert([
            [
                'superior_role_id' => Role::where('name', 'ceo')->first()->id,
                'subordinate_role_id' => Role::where('name', 'secretary-general')->first()->id
            ],
            [
                'superior_role_id' => Role::where('name', 'secretary-general')->first()->id,
                'subordinate_role_id' => Role::where('name', 'executive-director')->first()->id
            ],
            [
                'superior_role_id' => Role::where('name', 'executive-director')->first()->id,
                'subordinate_role_id' => Role::where('name', 'department-head')->first()->id
            ],
            [
                'superior_role_id' => Role::where('name', 'department-head')->first()->id,
                'subordinate_role_id' => Role::where('name', 'employee')->first()->id
            ],
        ]);


        Role::findByName('ceo')->givePermissionTo([
            // User Permissions
            'user.view',
            'user.index',

            // Department Permissions
            'department.view-any',
            'department.index',
            'department.view-subordinates',

            // Message Permissions (all can message)
            'message.create',
            'message.view',
            'message.reply',
            'message.index',
            'message.delete',

            // Task Permissions (subordinate level)
            'task.view-subordinates',
            'task.approve-subordinates',
            'task.comment',
            'task.index',
            'task.view',
            'task.view-history',
            'task.assign',

            // Executive Plan Permissions (subordinate level)
            'executive-plan.view-subordinates',
            'executive-plan.edit-subordinates',
            'executive-plan.export-subordinates'
        ]);


        Role::findByName('secretary-general')->givePermissionTo([
            // User Permissions
            'user.view',
            'user.index',

            // Department Permissions
            'department.view-any',
            'department.index',
            'department.view-subordinates',

            // Message Permissions (all can message)
            'message.create',
            'message.view',
            'message.reply',
            'message.index',
            'message.delete',

            // Task Permissions (subordinate level)
            'task.view-subordinates',
            'task.approve-subordinates',
            'task.comment',
            'task.index',
            'task.view',
            'task.view-history',
            'task.assign',

            // Executive Plan Permissions (subordinate level)
            'executive-plan.view-subordinates',
            'executive-plan.edit-subordinates',
            'executive-plan.export-subordinates'
        ]);

        Role::findByName('executive-director')->givePermissionTo([
            // User Permissions
            'user.view',
            'user.index',

            // Department Permissions
            'department.view-any',
            'department.index',
            'department.view-subordinates',

            // Message Permissions (all can message)
            'message.create',
            'message.view',
            'message.reply',
            'message.index',
            'message.delete',

            // Task Permissions (subordinate level)
            'task.view-subordinates',
            'task.approve-subordinates',
            'task.comment',
            'task.index',
            'task.view-history',
            'task.assign',

            // Executive Plan Permissions (subordinate level)
            'executive-plan.view-subordinates',
            'executive-plan.edit-subordinates',
            'executive-plan.export-subordinates'
        ]);

        Role::findByName('department-head')->givePermissionTo([
            // User Permissions
            'user.view',
            'user.index',

            // Department Permissions
            'department.view-members',

            // Task Permissions (department level only)
            'task.view-department',
            'task.edit-department',
            'task.comment-department',
            'task.index-department',
            'task.approve-department',
            'task.reject-department',
            'task.view-history',
            'task.assign',

            // Executive Plan Permissions (self and department level)
            'executive-plan.view-department',
            'executive-plan.edit-department',
            'executive-plan.export-department',
            'executive-plan.view-self',
            'executive-plan.edit-self',

            // Message Permissions
            'message.create',
            'message.view',
            'message.reply',
            'message.index'
        ]);

        Role::findByName('employee')->givePermissionTo([
            // User Permissions
            'user.view',
            'user.index',


            // Task Permissions (self level)
            'task.view',
            'task.edit',
            'task.comment',
            'task.index',
            'task.view-history',

            // Executive Plan Permissions (self level)
            'executive-plan.view-self',
            'executive-plan.edit-self',
            'executive-plan.export-self',

            // Message Permissions
            'message.create',
            'message.view',
            'message.reply',
            'message.index'
        ]);
    }
}
