<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_medicals')) {
            return;
        }
        Schema::create('attendance_medicals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->index();
            $table->integer('clinical_id')->nullable();
            $table->integer('medical_id')->nullable();
            $table->tinyInteger('emergence')->default(0);
            $table->string('dosage')->nullable();
            $table->tinyInteger('checked')->default(0);
            $table->tinyInteger('paid')->default(0);
            $table->integer('checker_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('paid_id')->nullable();
            $table->integer('creator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_medicals');
    }
};
