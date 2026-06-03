<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('insurance_prices')) {
            return;
        }
        Schema::create('insurance_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('insurance_id')->index();
            $table->string('type')->nullable();  // e.g. procedure, lab_test, medical
            $table->integer('target_id')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_prices');
    }
};
