<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'paid_bills'"))) {
            return;
        }
        // Denormalized audit/snapshot of paid attendance bills
        Schema::create('paid_bills', function (Blueprint $table) {
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
            $table->string('name')->nullable();
            $table->string('dosage')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('group')->nullable();
            $table->string('patient')->nullable();
            $table->string('gender')->nullable();
            $table->tinyInteger('closed')->default(0);
            $table->integer('closer_id')->nullable();
            $table->integer('patient_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paid_bills');
    }
};
