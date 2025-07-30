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
        Schema::create('executive_plan_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('executive_plan_cell_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('size');
            $table->string('extension')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('icon')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('executive_plan_attachments');
    }
};
