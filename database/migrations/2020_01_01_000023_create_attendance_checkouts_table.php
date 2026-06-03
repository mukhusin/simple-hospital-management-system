<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_checkouts')) {
            return;
        }
        Schema::create('attendance_checkouts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->index();
            $table->text('notes')->nullable();
            $table->integer('creator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_checkouts');
    }
};
