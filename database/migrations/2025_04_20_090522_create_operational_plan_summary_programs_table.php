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
        Schema::create('operational_plan_summary_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_plan_id')->constrained('operational_plans')->onDelete('cascade');
            $table->foreignId('strategic_goal_id')->constrained('operational_plan_strategic_goals')->onDelete('cascade');
            $table->string('title');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_plan_summary_programs');
    }
};
