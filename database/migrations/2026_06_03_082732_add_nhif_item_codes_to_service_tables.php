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
        if (Schema::hasColumn('procedures', 'nhif_item_code')) {
            return;
        }
        DB::statement("SET SESSION sql_mode=''");
        // Add NHIF item codes to procedures
        Schema::table('procedures', function (Blueprint $table) {
            $table->string('nhif_item_code', 20)->nullable()->after('price');
            $table->tinyInteger('nhif_item_type_id')->nullable()->comment('1=Lab,2=Procedure,3=Medicine,4=Bed Charge,5=Sundries')->after('nhif_item_code');
        });

        // Add NHIF item codes to lab_tests
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->string('nhif_item_code', 20)->nullable()->after('price');
            $table->tinyInteger('nhif_item_type_id')->nullable()->after('nhif_item_code');
        });

        // Add NHIF item codes to medicals (drugs)
        Schema::table('medicals', function (Blueprint $table) {
            $table->string('nhif_item_code', 20)->nullable()->after('price_unit');
            $table->tinyInteger('nhif_item_type_id')->nullable()->after('nhif_item_code');
        });

        // Add NHIF item codes to attendance_bills
        Schema::table('attendance_bills', function (Blueprint $table) {
            $table->string('nhif_item_code', 20)->nullable()->after('amount');
            $table->tinyInteger('nhif_item_type_id')->nullable()->after('nhif_item_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procedures', function (Blueprint $table) {
            $table->dropColumn(['nhif_item_code', 'nhif_item_type_id']);
        });
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->dropColumn(['nhif_item_code', 'nhif_item_type_id']);
        });
        Schema::table('medicals', function (Blueprint $table) {
            $table->dropColumn(['nhif_item_code', 'nhif_item_type_id']);
        });
        Schema::table('attendance_bills', function (Blueprint $table) {
            $table->dropColumn(['nhif_item_code', 'nhif_item_type_id']);
        });
    }
};
