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
        Schema::create('executive_plan_cells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_plan_column_id')->constrained('executive_plan_columns');
            $table->foreignId('user_id')->constrained('users');
            $table->text('value')->nullable();
            $table->timestamp('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('executive_plan_cells');
    }
};
