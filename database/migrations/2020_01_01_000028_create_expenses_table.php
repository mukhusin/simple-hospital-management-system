<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('expenses')) {
            return;
        }
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('creator_id')->nullable();
            $table->integer('updator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
