<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('patients')) {
            return;
        }
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('name');
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->integer('creator_id')->nullable();
            $table->integer('updator_id')->nullable();
            $table->string('address')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('occupation')->nullable();
            $table->string('card')->nullable();
            $table->string('sponsor')->nullable();
            $table->string('sponsor_code')->nullable();
            // NHIF fields
            $table->string('nhif_card_no')->nullable();
            $table->string('nhif_membership_no')->nullable();
            $table->string('nida_no')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('notes')->nullable();
            $table->integer('location_office_id')->nullable();
            $table->integer('location_user_id')->nullable();
            $table->integer('pause_id')->nullable();
            $table->text('allegies')->nullable();
            $table->string('country')->nullable();
            $table->string('race')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
