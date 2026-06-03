<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'medical_stocks_data'"))) {
            return;
        }
        // Denormalized snapshot of medical_stocks with resolved names
        Schema::create('medical_stocks_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('medical_id')->nullable()->index();
            $table->integer('stock')->default(0);
            $table->integer('changes')->default(0);
            $table->text('remark')->nullable();
            $table->integer('payment_id')->nullable();
            $table->integer('creator_id')->nullable();
            $table->integer('stock_before')->nullable();
            $table->string('creator')->nullable();
            $table->string('medicine')->nullable();
            $table->string('brand')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_stocks_data');
    }
};
