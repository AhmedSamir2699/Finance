<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sidebar_links', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('icon');
            $table->string('permission')->nullable();
            $table->enum('visibility', ['all', 'authenticated', 'guest'])->default('authenticated');
            $table->boolean('is_external')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('sidebar_links')->onDelete('cascade');
        });

        // Insert default sidebar links
        DB::table('sidebar_links')->insert([
            [
                'id' => 1,
                'title' => 'لوحة تحكم عامة',
                'url' => 'dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'permission' => null,
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 1,
                'parent_id' => null,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 2,
                'title' => 'المهام',
                'url' => 'tasks.index',
                'icon' => 'fas fa-tasks',
                'permission' => 'task.index',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 2,
                'parent_id' => null,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 6,
                'title' => 'الحضور والانصراف',
                'url' => 'timesheets.index',
                'icon' => 'fas fa-clock',
                'permission' => 'timesheet.index',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 5,
                'parent_id' => null,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 7,
                'title' => 'الرسائل',
                'url' => 'messages.index',
                'icon' => 'fas fa-envelope',
                'permission' => 'message.index',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 6,
                'parent_id' => null,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 9,
                'title' => 'التقارير',
                'url' => 'users.reports.index',
                'icon' => 'fas fa-chart-bar',
                'permission' => 'report.index',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 8,
                'parent_id' => null,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 10,
                'title' => 'التقييم',
                'url' => 'evaluate.random',
                'icon' => 'fas fa-star',
                'permission' => 'evaluate',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 9,
                'parent_id' => null,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 11,
                'title' => 'الطلبات',
                'url' => 'requests.index',
                'icon' => 'fas fa-file-alt',
                'permission' => 'request.index',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 10,
                'parent_id' => null,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 14,
                'title' => 'إنشاء مهمة',
                'url' => 'tasks.create',
                'icon' => 'fas fa-plus',
                'permission' => 'task.create',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 1,
                'parent_id' => 2,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 15,
                'title' => 'قائمة المهام',
                'url' => 'tasks.index',
                'icon' => 'fas fa-list',
                'permission' => 'task.index',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 3,
                'parent_id' => 2,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 16,
                'title' => 'مهام تحتاج موافقة',
                'url' => 'tasks.need-approval',
                'icon' => 'fas fa-check-circle',
                'permission' => 'task.approve-department',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 4,
                'parent_id' => 2,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 17,
                'title' => 'مهام الخطة التنفيذية',
                'url' => 'tasks.executive-plan',
                'icon' => 'fas fa-chart-line',
                'permission' => 'task.executive-plan',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 2,
                'parent_id' => 2,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 18,
                'title' => 'إنشاء طلب',
                'url' => 'requests.create',
                'icon' => 'fas fa-plus',
                'permission' => 'request.create',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 1,
                'parent_id' => 11,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
            [
                'id' => 19,
                'title' => 'سجل الطلبات',
                'url' => 'requests.index',
                'icon' => 'fas fa-history',
                'permission' => 'request.index',
                'visibility' => 'authenticated',
                'is_external' => 0,
                'is_active' => 1,
                'order' => 2,
                'parent_id' => 11,
                'created_at' => '2025-07-23 14:21:49',
                'updated_at' => '2025-07-23 15:26:45',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_links');
    }
}; 