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
        Schema::create('operational_plan_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('operational_plan_activities')->onDelete('cascade');
            $table->timestamp('completed_at')->useCurrent();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_plan_activity_logs');
    }
};
