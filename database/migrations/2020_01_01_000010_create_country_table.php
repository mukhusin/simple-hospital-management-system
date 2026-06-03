<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('country')) {
            return;
        }
        // Simple lookup table — only stores country names
        Schema::create('country', function (Blueprint $table) {
            $table->string('name')->primary();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country');
    }
};
