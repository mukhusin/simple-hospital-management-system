<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('patient_attendances')) {
            return;
        }
        Schema::create('patient_attendances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->index();
            $table->integer('creator_id')->nullable();
            $table->integer('pauser_id')->nullable();
            $table->tinyInteger('paused')->default(0);
            $table->integer('resumer_id')->nullable();
            $table->tinyInteger('closed')->default(0);
            $table->integer('closer_id')->nullable();
            $table->integer('doctor_id')->nullable();
            $table->tinyInteger('reattend')->default(0);
            $table->string('type')->nullable();
            $table->text('history')->nullable();
            $table->text('complain')->nullable();
            $table->text('review')->nullable();
            $table->text('general_observation')->nullable();
            $table->text('systemic_observation')->nullable();
            $table->text('comment')->nullable();
            $table->text('remark')->nullable();
            $table->text('provision')->nullable();
            $table->text('final')->nullable();
            $table->text('differential')->nullable();
            $table->text('test')->nullable();
            $table->text('followup')->nullable();
            $table->dateTime('followup_time')->nullable();
            $table->decimal('service_amount', 15, 2)->nullable();
            $table->text('service_remark')->nullable();
            $table->decimal('consultation_fee', 15, 2)->nullable();
            $table->tinyInteger('ward_served')->default(0);
            $table->integer('ward_id')->nullable();
            $table->string('bed')->nullable();
            $table->integer('insurance_id')->nullable();
            $table->string('insurance_number')->nullable();
            // NHIF fields
            $table->string('nhif_card_no')->nullable();
            $table->string('nhif_authorization_no')->nullable();
            $table->string('nhif_folio_no')->nullable();
            $table->integer('nhif_visit_type_id')->nullable();
            $table->string('nhif_claim_status')->nullable();
            $table->decimal('nhif_claimed_amount', 15, 2)->nullable();
            $table->json('nhif_verification_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_attendances');
    }
};
