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
        Schema::create('operational_plan_strategic_goals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('operational_plan_department_id')->nullable();
            $table->foreignId('operational_plan_id');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->foreign('operational_plan_department_id', 'opsal_sg_department_id_foreign')
                ->references('id')
                ->on('operational_plan_departments')
                ->onDelete('cascade');

            $table->foreign('operational_plan_id', 'opsal_sg_oplan_id_foreign')
                ->references('id')
                ->on('operational_plans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_plan_strategic_goals');
    }
};
