<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop columns from executive_plan_cells table if it exists
        if (Schema::hasTable('executive_plan_cells')) {
            Schema::table('executive_plan_cells', function (Blueprint $table) {
                $table->dropColumn(['proofs', 'history', 'status']);
            });
        }

        // Delete the entire executive_plan_attachments folder and all its contents
        if (Storage::disk('public')->exists('executive_plan_attachments')) {
            Storage::disk('public')->deleteDirectory('executive_plan_attachments');
        }

        // Drop the executive_plan_attachments table if it exists
        Schema::dropIfExists('executive_plan_attachments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the executive_plan_attachments table
        Schema::create('executive_plan_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('executive_plan_cell_id');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->bigInteger('size');
            $table->timestamps();
            
            $table->foreign('executive_plan_cell_id')->references('id')->on('executive_plan_cells')->onDelete('cascade');
        });

        // Add back the columns to executive_plan_cells table if it exists
        if (Schema::hasTable('executive_plan_cells')) {
            Schema::table('executive_plan_cells', function (Blueprint $table) {
                $table->json('proofs')->nullable();
                $table->json('history')->nullable();
                $table->string('status')->default('pending');
            });
        }
    }
}; 