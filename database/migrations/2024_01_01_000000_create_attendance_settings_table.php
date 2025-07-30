<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('scope_type')->default('global'); // 'global', 'department', 'user'
            $table->unsignedBigInteger('scope_id')->nullable(); // null for global, or department_id/user_id
            $table->integer('late_arrival_tolerance')->default(10); // in minutes
            $table->integer('early_leave_tolerance')->default(5); // in minutes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_settings');
    }
}; 