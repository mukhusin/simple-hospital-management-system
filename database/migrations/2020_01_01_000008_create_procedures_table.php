<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('procedures')) {
            return;
        }
        Schema::create('procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);
            // NHIF fields
            $table->string('nhif_item_code')->nullable();
            $table->integer('nhif_item_type_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
