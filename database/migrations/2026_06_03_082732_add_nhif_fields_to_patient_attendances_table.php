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
        if (Schema::hasColumn('patient_attendances', 'nhif_card_no')) {
            return;
        }
        DB::statement("SET SESSION sql_mode=''");
        Schema::table('patient_attendances', function (Blueprint $table) {
            $table->string('nhif_card_no', 30)->nullable()->after('insurance_number');
            $table->string('nhif_authorization_no', 50)->nullable()->after('nhif_card_no');
            $table->unsignedBigInteger('nhif_folio_no')->nullable()->after('nhif_authorization_no');
            $table->tinyInteger('nhif_visit_type_id')->nullable()->comment('1=Outpatient,2=Inpatient,3=Dental,4=Optical')->after('nhif_folio_no');
            $table->string('nhif_claim_status', 20)->nullable()->comment('pending,submitted,confirmed,signed')->after('nhif_visit_type_id');
            $table->decimal('nhif_claimed_amount', 12, 2)->nullable()->after('nhif_claim_status');
            $table->json('nhif_verification_data')->nullable()->after('nhif_claimed_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_attendances', function (Blueprint $table) {
            $table->dropColumn([
                'nhif_card_no', 'nhif_authorization_no', 'nhif_folio_no',
                'nhif_visit_type_id', 'nhif_claim_status', 'nhif_claimed_amount', 'nhif_verification_data'
            ]);
        });
    }
};
