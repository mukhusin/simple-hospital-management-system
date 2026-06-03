<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('insurances')) {
            return;
        }
        Schema::create('insurances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('consultation_fee', 15, 2)->default(0);
            $table->decimal('specialist_fee', 15, 2)->default(0);
            // NHIF-specific fields
            $table->string('nhif_scheme_id')->nullable();
            $table->string('nhif_product_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
