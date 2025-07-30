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
        Schema::create('operational_plan_summary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('summary_program_id')->constrained('operational_plan_summary_programs')->onDelete('cascade');
            $table->string('title');
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('detailed_expected_cost', 12, 2)->default(0);
            $table->decimal('total_expected_cost', 12, 2)->default(0);
            $table->decimal('detailed_expected_revenue', 12, 2)->default(0);
            $table->decimal('total_expected_revenue', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_plan_summary_items');
    }
};
