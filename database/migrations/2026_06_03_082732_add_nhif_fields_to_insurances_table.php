<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('insurances', 'nhif_scheme_id')) {
            return;
        }
        DB::statement("SET SESSION sql_mode=''");
        Schema::table('insurances', function (Blueprint $table) {
            $table->string('nhif_scheme_id', 20)->nullable()->after('specialist_fee');
            $table->string('nhif_product_code', 20)->nullable()->after('nhif_scheme_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insurances', function (Blueprint $table) {
            $table->dropColumn(['nhif_scheme_id', 'nhif_product_code']);
        });
    }
};
