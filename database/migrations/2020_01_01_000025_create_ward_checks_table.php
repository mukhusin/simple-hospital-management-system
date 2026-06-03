<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ward_checks')) {
            return;
        }
        Schema::create('ward_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ward_id')->nullable()->index();
            $table->integer('attendance_id')->nullable()->index();
            $table->decimal('bill', 15, 2)->default(0);
            $table->string('bed')->nullable();
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->integer('creator_id')->nullable();
            $table->integer('updator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ward_checks');
    }
};
