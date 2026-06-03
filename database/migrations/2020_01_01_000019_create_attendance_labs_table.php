<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_labs')) {
            return;
        }
        Schema::create('attendance_labs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->index();
            $table->integer('clinical_id')->nullable();
            $table->integer('test_id')->nullable();
            $table->text('results')->nullable();
            $table->text('remark')->nullable();
            $table->integer('creator_id')->nullable();
            $table->integer('updator_id')->nullable();
            $table->tinyInteger('sample_register')->default(0);
            $table->integer('sample_register_id')->nullable();
            $table->tinyInteger('results_register')->default(0);
            $table->integer('result_register_id')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_labs');
    }
};
