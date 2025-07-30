<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operational_plan_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_plan_department_id');
            $table->foreignId('operational_plan_strategic_goal_id');
            $table->string('title');
            $table->decimal('budget', 12, 2)->default(0);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->foreign('operational_plan_department_id', 'opsal_department_id_fk')
                ->references('id')
                ->on('operational_plan_departments')
                ->onDelete('cascade');
            $table->foreign('operational_plan_strategic_goal_id', 'opsal_strategic_goal_id_fk')
                ->references('id')
                ->on('operational_plan_strategic_goals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_plan_programs');
    }
};
