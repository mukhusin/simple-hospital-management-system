<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'attendance_payment_data'"))) {
            return;
        }
        // Denormalized snapshot of attendance_payments with resolved names
        Schema::create('attendance_payment_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->nullable()->index();
            $table->integer('creator_id')->nullable();
            $table->decimal('tendered', 15, 2)->default(0);
            $table->integer('dispense_id')->nullable();
            $table->decimal('bill', 15, 2)->default(0);
            $table->decimal('paid', 15, 2)->default(0);
            $table->string('mode')->nullable();
            $table->integer('insurance_id')->nullable();
            $table->string('receipt')->nullable();
            $table->string('code')->nullable();
            // Denormalized
            $table->string('patient')->nullable();
            $table->string('gender')->nullable();
            $table->string('creator')->nullable();
            $table->integer('attendance_creator_id')->nullable();
            $table->tinyInteger('reattend')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_payment_data');
    }
};
