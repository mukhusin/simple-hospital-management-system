<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('medical_stocks')) {
            return;
        }
        Schema::create('medical_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('medical_id')->index();
            $table->integer('stock')->default(0);
            $table->integer('changes')->default(0);
            $table->text('remark')->nullable();
            $table->integer('payment_id')->nullable();
            $table->integer('creator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_stocks');
    }
};
