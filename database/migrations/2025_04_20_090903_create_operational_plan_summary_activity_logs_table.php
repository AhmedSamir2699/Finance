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
        // Schema::create('operational_plan_summary_activity_logs', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('summary_activity_id');
        //     $table->timestamp('completed_at')->useCurrent();
        //     $table->text('notes')->nullable();

        //     $table->foreign('summary_activity_id', 'opsal_summary_activity_id_fk')
        //     ->references('id')
        //     ->on('operational_plan_summary_activities')
        //     ->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_plan_summary_activity_logs');
    }
};
