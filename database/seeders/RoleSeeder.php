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

        Role::create([
            'name' => 'super-admin',
            'display_name' => 'مدير النظام'
        ]);


        Role::create([
            'name' => 'ceo',
            'display_name' => 'الرئيس التنفيذي'
        ]);
        Role::create([
            'display_name' => 'الأمين العام',
            'name' => 'secretary-general'
        ]);
        Role::create([
            'display_name' => 'المدير التنفيذي',
            'name' => 'executive-director'
        ]);

        Role::create([
            'name' => 'department-head',
            'display_name' => 'رئيس القسم'
        ]);
        Role::create([
            'name' => 'employee',
            'display_name' => 'موظف'
        ]);


        // User Permissions
        Permission::create(['name' => 'user.impersonate', 'display_name' => 'التنقل كمستخدم آخر']);

        Permission::create(['name' => 'user.create', 'display_name' => 'إنشاء مستخدم']);
        Permission::create(['name' => 'user.edit', 'display_name' => 'تعديل مستخدم']);
        Permission::create(['name' => 'user.delete', 'display_name' => 'حذف مستخدم']);
        Permission::create(['name' => 'user.view', 'display_name' => 'عرض مستخدم']);
        Permission::create(['name' => 'user.index', 'display_name' => 'عرض جميع المستخدمين']);
        Permission::create(['name' => 'user.assign-role', 'display_name' => 'تعيين دور للمستخدم']);
        Permission::create(['name' => 'user.remove-role', 'display_name' => 'إزالة دور من المستخدم']);
        Permission::create(['name' => 'user.assign-department', 'display_name' => 'تعيين قسم للمستخدم']);

        // Department Permissions
        Permission::create(['name' => 'department.view-self', 'display_name' => 'عرض القسم الخاص']);
        Permission::create(['name' => 'department.view-any', 'display_name' => 'عرض الأقسام لأي شخص']);
        Permission::create(['name' => 'department.view-subordinates', 'display_name' => 'عرض الأقسام للمرؤوسين']);
        Permission::create(['name' => 'department.view-members', 'display_name' => 'عرض أعضاء القسم']);
        Permission::create(['name' => 'department.create', 'display_name' => 'إنشاء قسم']);
        Permission::create(['name' => 'department.edit', 'display_name' => 'تعديل قسم']);
        Permission::create(['name' => 'department.delete', 'display_name' => 'حذف قسم']);
        Permission::create(['name' => 'department.index', 'display_name' => 'عرض جميع الأقسام']);

        // Role Permissions
        Permission::create(['name' => 'role.create', 'display_name' => 'إنشاء دور']);
        Permission::create(['name' => 'role.edit', 'display_name' => 'تعديل دور']);
        Permission::create(['name' => 'role.delete', 'display_name' => 'حذف دور']);
        Permission::create(['name' => 'role.view', 'display_name' => 'عرض دور']);
        Permission::create(['name' => 'role.index', 'display_name' => 'عرض جميع الأدوار']);
        Permission::create(['name' => 'role.assign-permission', 'display_name' => 'تعيين صلاحية للدور']);

        // Message Permissions
        Permission::create(['name' => 'message.create', 'display_name' => 'إنشاء رسالة']);
        Permission::create(['name' => 'message.delete', 'display_name' => 'حذف رسالة']);
        Permission::create(['name' => 'message.view', 'display_name' => 'عرض رسالة']);
        Permission::create(['name' => 'message.index', 'display_name' => 'عرض جميع الرسائل']);
        Permission::create(['name' => 'message.reply', 'display_name' => 'الرد على الرسالة']);

        // Request Permissions
        Permission::create(['name' => 'request.create', 'display_name' => 'إنشاء طلب']);
        Permission::create(['name' => 'request.edit', 'display_name' => 'تعديل طلب']);
        Permission::create(['name' => 'request.delete', 'display_name' => 'حذف طلب']);
        Permission::create(['name' => 'request.view', 'display_name' => 'عرض طلب']);
        Permission::create(['name' => 'request.view-any', 'display_name' => 'عرض طلب لأي شخص']);
        Permission::create(['name' => 'request.index', 'display_name' => 'عرض جميع الطلبات']);
        Permission::create(['name' => 'request.approve', 'display_name' => 'إعتماد الطلب']);

        // Task Permissions
        Permission::create(['name' => 'task.create', 'display_name' => 'إنشاء مهمة']);
        Permission::create(['name' => 'task.edit', 'display_name' => 'تعديل مهمة']);
        Permission::create(['name' => 'task.delete', 'display_name' => 'حذف مهمة']);
        Permission::create(['name' => 'task.view', 'display_name' => 'عرض مهمة']);
        Permission::create(['name' => 'task.index', 'display_name' => 'عرض جميع المهام']);
        Permission::create(['name' => 'task.assign', 'display_name' => 'تعيين مهمة']);
        Permission::create(['name' => 'task.view-any', 'display_name' => 'عرض المهام لأي شخص']);
        Permission::create(['name' => 'task.edit-any', 'display_name' => 'تعديل المهام لأي شخص']);
        Permission::create(['name' => 'task.create-any', 'display_name' => 'إنشاء مهمة لأي شخص']);
        Permission::create(['name' => 'task.delete-any', 'display_name' => 'حذف المهام لأي شخص']);
        Permission::create(['name' => 'task.view-department', 'display_name' => 'عرض المهام للقسم']);
        Permission::create(['name' => 'task.edit-department', 'display_name' => 'تعديل المهام للقسم']);
        Permission::create(['name' => 'task.create-department', 'display_name' => 'إنشاء مهمة للقسم']);
        Permission::create(['name' => 'task.delete-department', 'display_name' => 'حذف المهام للقسم']);
        Permission::create(['name' => 'task.index-department', 'display_name' => 'عرض المهام للقسم']);
        Permission::create(['name' => 'task.approve-department', 'display_name' => 'إعتماد المهمة للقسم']);
        Permission::create(['name' => 'task.reject-department', 'display_name' => 'رفض المهمة للقسم']);
        Permission::create(['name' => 'task.comment-department', 'display_name' => 'التعليق على المهمة للقسم']);
        Permission::create(['name' => 'task.view-subordinates', 'display_name' => 'عرض المهام للمرؤوسين']);
        Permission::create(['name' => 'task.approve-subordinates', 'display_name' => 'إعتماد المهمة للمرؤوسين']);
        Permission::create(['name' => 'task.comment', 'display_name' => 'التعليق على المهمة']);
        Permission::create(['name' => 'task.view-history', 'display_name' => 'عرض تاريخ المهمة']);

        // Executive Plan Permissions
        Permission::create(['name' => 'executive-plan.view-self', 'display_name' => 'عرض الخطة التنفيذية الخاصة']);
        Permission::create(['name' => 'executive-plan.edit-self', 'display_name' => 'تعديل الخطة التنفيذية الخاصة']);
        Permission::create(['name' => 'executive-plan.export-self', 'display_name' => 'تصدير الخطة التنفيذية الخاصة']);
        Permission::create(['name' => 'executive-plan.view-any', 'display_name' => 'عرض الخطة التنفيذية لأي شخص']);
        Permission::create(['name' => 'executive-plan.edit-any', 'display_name' => 'تعديل الخطة التنفيذية لأي شخص']);
        Permission::create(['name' => 'executive-plan.export-any', 'display_name' => 'تصدير الخطة التنفيذية لأي شخص']);
        Permission::create(['name' => 'executive-plan.view-department', 'display_name' => 'عرض الخطة التنفيذية للقسم']);
        Permission::create(['name' => 'executive-plan.edit-department', 'display_name' => 'تعديل الخطة التنفيذية للقسم']);
        Permission::create(['name' => 'executive-plan.export-department', 'display_name' => 'تصدير الخطة التنفيذية للقسم']);
        Permission::create(['name' => 'executive-plan.view-subordinates', 'display_name' => 'عرض الخطة التنفيذية للمرؤوسين']);
        Permission::create(['name' => 'executive-plan.edit-subordinates', 'display_name' => 'تعديل الخطة التنفيذية للمرؤوسين']);
        Permission::create(['name' => 'executive-plan.export-subordinates', 'display_name' => 'تصدير الخطة التنفيذية للمرؤوسين']);


        // Management Permissions
        Permission::create(['name' => 'manage.executive-plan', 'display_name' => 'إدارة الخطة التنفيذية']);
        Permission::create(['name' => 'manage.departments', 'display_name' => 'إدارة الأقسام']);
        Permission::create(['name' => 'manage.users', 'display_name' => 'إدارة المستخدمين']);
        Permission::create(['name' => 'manage.roles', 'display_name' => 'إدارة الأدوار']);
        Permission::create(['name' => 'manage.permissions', 'display_name' => 'إدارة الصلاحيات']);
        Permission::create(['name' => 'manage.forms', 'display_name' => 'إدارة النماذج']);
        Permission::create(['name' => 'manage.logs', 'display_name' => 'إدارة السجلات']);
        Permission::create(['name' => 'manage.settings', 'display_name' => 'إدارة الإعدادات']);
        

        // Evaluation Permissions
        Permission::create(['name' => 'evaluate', 'display_name' => 'تقييم الأداء']);
        Permission::create(['name' => 'evaluate.view', 'display_name' => 'عرض تقييم الأداء']);
        Permission::create(['name' => 'evaluate.create', 'display_name' => 'إنشاء تقييم الأداء']);
        Permission::create(['name' => 'evaluate.edit', 'display_name' => 'تعديل تقييم الأداء']);
        Permission::create(['name' => 'evaluate.delete', 'display_name' => 'حذف تقييم الأداء']);
        Permission::create(['name' => 'evaluate.index', 'display_name' => 'عرض جميع تقييمات الأداء']);

        
        // Daily Evaluation Report Permissions
        Permission::create(['name' => 'daily-evaluation-report.view', 'display_name' => 'عرض تقرير التقييم اليومي']);
        Permission::create(['name' => 'daily-evaluation-report.export', 'display_name' => 'تصدير تقرير التقييم اليومي']);
        Permission::create(['name' => 'daily-evaluation-report.index', 'display_name' => 'عرض جميع تقارير التقييم اليومي']);
        Permission::create(['name' => 'daily-evaluation-report.view-any', 'display_name' => 'عرض تقرير التقييم اليومي لأي شخص']);
        Permission::create(['name' => 'daily-evaluation-report.export-any', 'display_name' => 'تصدير تقرير التقييم اليومي لأي شخص']);

        // Monthly Evaluation Report Permissions
        Permission::create(['name' => 'monthly-evaluation-report.view', 'display_name' => 'عرض تقرير التقييم الشهري']);
        Permission::create(['name' => 'monthly-evaluation-report.export', 'display_name' => 'تصدير تقرير التقييم الشهري']);
        Permission::create(['name' => 'monthly-evaluation-report.index', 'display_name' => 'عرض جميع تقارير التقييم الشهري']);
        Permission::create(['name' => 'monthly-evaluation-report.view-any', 'display_name' => 'عرض تقرير التقييم الشهري لأي شخص']);
        Permission::create(['name' => 'monthly-evaluation-report.export-any', 'display_name' => 'تصدير تقرير التقييم الشهري لأي شخص']);

        // Yearly Evaluation Report Permissions
        Permission::create(['name' => 'yearly-evaluation-report.view', 'display_name' => 'عرض تقرير التقييم السنوي']);
        Permission::create(['name' => 'yearly-evaluation-report.export', 'display_name' => 'تصدير تقرير التقييم السنوي']);
        Permission::create(['name' => 'yearly-evaluation-report.index', 'display_name' => 'عرض جميع تقارير التقييم السنوي']);
        Permission::create(['name' => 'yearly-evaluation-report.view-any', 'display_name' => 'عرض تقرير التقييم السنوي لأي شخص']);
        Permission::create(['name' => 'yearly-evaluation-report.export-any', 'display_name' => 'تصدير تقرير التقييم السنوي لأي شخص']);

        // Summary Report Permissions
        Permission::create(['name' => 'summary.view', 'display_name' => 'عرض التقرير الملخص']);




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
