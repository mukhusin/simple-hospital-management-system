<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::hasTable can fail for this table; use raw query instead
        $exists = DB::select("SHOW TABLES LIKE 'new_insurances_prices'");
        if (!empty($exists)) {
            return;
        }
        Schema::create('new_insurances_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('price', 15, 2)->default(0);
            $table->string('type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_insurances_prices');
    }
};
