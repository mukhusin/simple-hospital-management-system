<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'patient_attendances_data'"))) {
            return;
        }
        // Denormalized snapshot of patient_attendances with resolved names
        Schema::create('patient_attendances_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->nullable()->index();
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
            // Denormalized resolved names
            $table->string('patient')->nullable();
            $table->string('gender')->nullable();
            $table->string('creator')->nullable();
            $table->string('closer')->nullable();
            $table->string('doctor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_attendances_data');
    }
};
