<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'attendance_labs_data'"))) {
            return;
        }
        // Denormalized snapshot of attendance_labs with resolved names
        Schema::create('attendance_labs_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->nullable()->index();
            $table->integer('clinical_id')->nullable();
            $table->integer('test_id')->nullable();
            $table->text('results')->nullable();
            $table->text('remark')->nullable();
            $table->integer('creator_id')->nullable();
            $table->integer('updator_id')->nullable();
            $table->tinyInteger('sample_register')->default(0);
            $table->integer('sample_register_id')->nullable();
            $table->tinyInteger('results_register')->default(0);
            $table->integer('result_register_id')->nullable();
            $table->string('attachment')->nullable();
            // Denormalized
            $table->string('test')->nullable();
            $table->string('patient')->nullable();
            $table->string('gender')->nullable();
            $table->string('doctor')->nullable();
            $table->string('technician')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_labs_data');
    }
};
