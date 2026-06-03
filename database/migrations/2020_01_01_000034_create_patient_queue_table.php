<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'patient_queue'"))) {
            return;
        }
        // Denormalized patient movement queue snapshot
        Schema::create('patient_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->nullable()->index();
            $table->integer('from_id')->nullable();
            $table->integer('from_office_id')->nullable();
            $table->integer('to_office_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->nullable();
            $table->string('patient')->nullable();
            $table->string('gender')->nullable();
            $table->integer('patient_id')->nullable();
            $table->string('from_user')->nullable();
            $table->string('to_office')->nullable();
            $table->string('from_office')->nullable();
            $table->tinyInteger('closed')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_queue');
    }
};
