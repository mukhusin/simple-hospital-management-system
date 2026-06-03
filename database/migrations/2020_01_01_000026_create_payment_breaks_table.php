<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_breaks')) {
            return;
        }
        Schema::create('payment_breaks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payment_id')->nullable()->index();
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_breaks');
    }
};
