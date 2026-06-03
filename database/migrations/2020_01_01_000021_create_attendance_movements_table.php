<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_movements')) {
            return;
        }
        Schema::create('attendance_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->index();
            $table->integer('from_id')->nullable();
            $table->integer('from_office_id')->nullable();
            $table->integer('to_office_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_movements');
    }
};
