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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->text('content');

            $table->foreignId('user_id')->constrained();

            $table->string('action_route')->nullable();
            $table->string('action_params')->nullable();

            $table->boolean('is_read')->default(false);
            $table->boolean('is_seen')->default(false);
            
            $table->timestamp('read_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
