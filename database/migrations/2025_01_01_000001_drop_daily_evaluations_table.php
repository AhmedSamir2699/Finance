<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verify data migration was successful before dropping
        if (Schema::hasTable('daily_evaluations') && Schema::hasTable('evaluation_scores')) {
            $oldCount = DB::table('daily_evaluations')->count();
            $newCount = DB::table('evaluation_scores')->count();
            
            // Only drop if we have migrated data or no data existed
            if ($oldCount === 0 || $newCount >= $oldCount) {
                Schema::dropIfExists('daily_evaluations');
                Log::info("Dropped daily_evaluations table. Old records: {$oldCount}, New records: {$newCount}");
            } else {
                Log::warning("Not dropping daily_evaluations table. Data migration incomplete. Old: {$oldCount}, New: {$newCount}");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the old table structure if needed
        Schema::create('daily_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('appearance')->default(0);
            $table->timestamps();
        });
    }
}; 