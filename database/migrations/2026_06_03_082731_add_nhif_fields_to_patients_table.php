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
        if (Schema::hasColumn('patients', 'nhif_card_no')) {
            return; // Already added (e.g. fresh install via baseline migration)
        }
        DB::statement("SET SESSION sql_mode=''");
        Schema::table('patients', function (Blueprint $table) {
            $table->string('nhif_card_no', 30)->nullable()->after('sponsor_code');
            $table->string('nhif_membership_no', 30)->nullable()->after('nhif_card_no');
            $table->string('nida_no', 20)->nullable()->after('nhif_membership_no');
            $table->unique('nhif_card_no');
            $table->index('nhif_card_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropUnique(['nhif_card_no']);
            $table->dropIndex(['nhif_card_no']);
            $table->dropColumn(['nhif_card_no', 'nhif_membership_no', 'nida_no']);
        });
    }
};
