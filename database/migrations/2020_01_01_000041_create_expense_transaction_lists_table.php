<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'expense_transaction_lists'"))) {
            return;
        }
        // Denormalized snapshot of expense_transactions with resolved names
        Schema::create('expense_transaction_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('expense_id')->nullable()->index();
            $table->text('remark')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('creator_id')->nullable();
            $table->string('name')->nullable();  // expense name
            $table->string('creator')->nullable(); // user name
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_transaction_lists');
    }
};
