<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_payments')) {
            return;
        }
        Schema::create('attendance_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->index();
            $table->integer('creator_id')->nullable();
            $table->decimal('tendered', 15, 2)->default(0);
            $table->integer('dispense_id')->nullable();
            $table->decimal('bill', 15, 2)->default(0);
            $table->decimal('paid', 15, 2)->default(0);
            $table->string('mode')->nullable();  // cash, insurance, etc.
            $table->integer('insurance_id')->nullable();
            $table->string('receipt')->nullable();
            $table->string('code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_payments');
    }
};
