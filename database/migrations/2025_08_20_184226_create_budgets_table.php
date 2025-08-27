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
       Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('finance_item_id'); // FK to finance_items
            $table->string('goal')->nullable();            // Goal description

            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('finance_item_id')
                  ->references('id')
                  ->on('finance_items')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('budgets');
    }
};
