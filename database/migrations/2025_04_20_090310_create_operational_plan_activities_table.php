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
        Schema::create('operational_plan_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_program_id')->constrained('operational_plan_sub_programs')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('yearly_target')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_plan_activities');
    }
};
