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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamp('task_date');
            $table->timestamp('due_date')->nullable();

            $table->foreignId('user_id')->constrained('users');
            
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->enum('type', ['scheduled', 'unscheduled', 'continous', 'training'])->default('scheduled');
            $table->integer('estimated_time')->nullable();
            $table->integer('actual_time')->nullable();

            $table->integer('completion_percentage')->nullable();
            $table->integer('quality_percentage')->nullable();

            $table->longText('description')->nullable();
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('assigned_by')->nullable()->constrained('users');

            $table->json('proofs')->nullable();
            $table->enum('status', ['pending','in_progress', 'submitted', 'approved','rejected'])->default('pending');


            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
