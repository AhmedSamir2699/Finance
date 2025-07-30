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
        // 1. Create evaluation_criteria table
        if (!Schema::hasTable('evaluation_criteria')) {
            Schema::create('evaluation_criteria', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('min_value')->default(0);
                $table->integer('max_value')->default(100);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Create evaluation_scores table
        if (!Schema::hasTable('evaluation_scores')) {
            Schema::create('evaluation_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('criteria_id')->constrained('evaluation_criteria')->cascadeOnDelete();
                $table->integer('score');
                $table->date('evaluated_at');
                $table->timestamps();
                $table->unique(['user_id', 'criteria_id', 'evaluated_at']);
            });
        }

        // 3. Insert default "Appearance" criteria (if not exists)
        if (!DB::table('evaluation_criteria')->where('name', 'Appearance')->exists()) {
            DB::table('evaluation_criteria')->insert([
                'name' => 'Appearance',
                'description' => 'Employee appearance evaluation',
                'min_value' => 0,
                'max_value' => 100,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Migrate existing appearance data to new system
        $appearanceCriteria = DB::table('evaluation_criteria')->where('name', 'Appearance')->first();
        
        if ($appearanceCriteria) {
            $appearanceCriteriaId = $appearanceCriteria->id;
            
            // Check if daily_evaluations table exists and has data
            if (Schema::hasTable('daily_evaluations')) {
                $existingEvaluations = DB::table('daily_evaluations')->count();
                $existingScores = DB::table('evaluation_scores')->count();
                
                // Only migrate if we haven't already migrated data
                if ($existingEvaluations > 0 && $existingScores === 0) {
                    // Migrate existing data
                    DB::statement("
                        INSERT INTO evaluation_scores (user_id, criteria_id, score, evaluated_at, created_at, updated_at)
                        SELECT 
                            user_id,
                            {$appearanceCriteriaId} as criteria_id,
                            appearance as score,
                            DATE(created_at) as evaluated_at,
                            created_at,
                            updated_at
                        FROM daily_evaluations
                        WHERE appearance IS NOT NULL
                    ");
                    
                    // Log migration results
                    $migratedCount = DB::table('evaluation_scores')->count();
                    Log::info("Evaluation migration completed: {$existingEvaluations} records migrated to {$migratedCount} evaluation scores");
                } else {
                    Log::info("Evaluation migration skipped: existingEvaluations={$existingEvaluations}, existingScores={$existingScores}");
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_scores');
        Schema::dropIfExists('evaluation_criteria');
    }
}; 