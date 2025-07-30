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
        Schema::table('operational_plans', function (Blueprint $table) {
                $table->string('title');
                $table->text('description')->nullable();
                $table->boolean('is_public')->default(false);
                $table->unsignedInteger('views')->default(0);
                $table->string('period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_plans', function (Blueprint $table) {
            //
        });
    }
};
