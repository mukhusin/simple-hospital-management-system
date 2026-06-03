<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!empty(DB::select("SHOW TABLES LIKE 'users_data'"))) {
            return;
        }
        // Denormalized snapshot of users with resolved office/role names
        Schema::create('users_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('level')->default(0);  // 0=staff, 1=admin
            $table->integer('office_id')->nullable();
            $table->integer('role_id')->nullable();
            $table->tinyInteger('hide')->default(0);
            $table->string('remember_token')->nullable();
            $table->string('office')->nullable();  // resolved office name
            $table->string('role')->nullable();    // resolved role name
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_data');
    }
};
