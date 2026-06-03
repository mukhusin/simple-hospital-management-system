<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_clinicals')) {
            return;
        }
        Schema::create('attendance_clinicals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->index();
            $table->text('general_observation')->nullable();
            $table->text('systemic_observation')->nullable();
            $table->text('comment')->nullable();
            $table->text('remark')->nullable();
            $table->text('history')->nullable();
            $table->text('complain')->nullable();
            $table->text('review')->nullable();
            $table->text('provision')->nullable();
            $table->text('differential')->nullable();
            $table->text('final')->nullable();
            $table->integer('creator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_clinicals');
    }
};
