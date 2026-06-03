<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            return;
        }
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('doctor_rate', 15, 2)->default(0);
            $table->decimal('bed_rate', 15, 2)->default(0);
            $table->decimal('specialist_rate', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
