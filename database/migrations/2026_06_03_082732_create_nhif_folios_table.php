<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nhif_folios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('attendance_id')->index();
            $table->unsignedBigInteger('folio_no')->nullable()->unique();
            $table->string('patient_card_no', 30);
            $table->string('authorization_no', 50)->nullable();
            $table->smallInteger('claim_year');
            $table->tinyInteger('claim_month');
            $table->decimal('amount_claimed', 12, 2)->default(0);
            $table->string('status', 20)->default('pending')->comment('pending,submitted,confirmed,signed,rejected');
            $table->json('folio_items')->nullable()->comment('JSON array of folio line items');
            $table->json('diseases')->nullable()->comment('JSON array of diagnosis codes');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->bigInteger('creator_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhif_folios');
    }
};
