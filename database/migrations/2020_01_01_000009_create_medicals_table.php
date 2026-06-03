<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('medicals')) {
            return;
        }
        Schema::create('medicals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('brand')->nullable();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->decimal('price_unit', 15, 2)->default(0);
            // NHIF fields
            $table->string('nhif_item_code')->nullable();
            $table->integer('nhif_item_type_id')->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicals');
    }
};
