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
        Schema::create('income_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_id')->constrained('incomes')->cascadeOnDelete();
            $table->foreignId('finance_item_id')->constrained('finance_items')->restrictOnDelete();
            $table->decimal('percentage', 6, 3); // e.g. 12.5%, supports up to 999.999
            $table->decimal('amount', 15, 2);    // computed: income.amount * percentage/100
            $table->timestamps();

            $table->unique(['income_id', 'finance_item_id']); // one row per item per income
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_allocations');
    }
};
