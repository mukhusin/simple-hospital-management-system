<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('in_sessions')) {
            return;
        }
        Schema::create('in_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('office_id')->nullable()->index();
            $table->integer('user_id')->nullable()->index();
            $table->tinyInteger('hide')->default(0);
            $table->text('changes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('in_sessions');
    }
};
