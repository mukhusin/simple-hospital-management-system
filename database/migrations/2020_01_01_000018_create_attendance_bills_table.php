<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_bills')) {
            return;
        }
        Schema::create('attendance_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->index();
            $table->string('name');
            $table->string('dosage')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            // NHIF fields
            $table->string('nhif_item_code')->nullable();
            $table->integer('nhif_item_type_id')->nullable();
            $table->tinyInteger('clear')->default(0);
            $table->integer('clear_id')->nullable();
            $table->string('group')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_bills');
    }
};
