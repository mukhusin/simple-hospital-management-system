<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'patient_bills'"))) {
            return;
        }
        // Denormalized snapshot of attendance_bills with patient info
        Schema::create('patient_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('dosage')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->tinyInteger('clear')->default(0);
            $table->integer('clear_id')->nullable();
            $table->string('group')->nullable();
            $table->integer('patient_id')->nullable();
            $table->string('patient')->nullable();
            $table->string('gender')->nullable();
            $table->tinyInteger('closed')->default(0);
            $table->integer('closer_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_bills');
    }
};
