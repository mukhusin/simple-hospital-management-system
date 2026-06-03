<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('expense_transactions')) {
            return;
        }
        Schema::create('expense_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('expense_id')->index();
            $table->text('remark')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('creator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_transactions');
    }
};
