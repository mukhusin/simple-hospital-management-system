<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('patient_vitals')) {
            return;
        }
        Schema::create('patient_vitals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->index();
            $table->string('temperature')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('blood')->nullable();
            $table->integer('creator_id')->nullable();
            $table->integer('updator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_vitals');
    }
};
