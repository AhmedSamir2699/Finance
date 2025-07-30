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
        Schema::table('executive_plan_cells', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->longText('description')->nullable();
            $table->json('proofs')->nullable();
            $table->json('history')->nullable();
        });
    }

    /**
     * Reverse the migrations.+
     */
    public function down(): void
    {
        Schema::table('executive_plan_cells', function (Blueprint $table) {
            //
        });
    }
};



