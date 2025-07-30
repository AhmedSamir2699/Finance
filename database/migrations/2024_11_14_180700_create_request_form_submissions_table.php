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
        Schema::create('request_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_form_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['in_progress', 'needs_attention','approved', 'rejected'])->default('in_progress');
            $table->json('fields');
            $table->integer('current_step')->default(1);
            $table->json('steps');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_form_submissions');
    }
};
