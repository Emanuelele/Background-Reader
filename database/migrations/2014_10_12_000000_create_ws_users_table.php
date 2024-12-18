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
        Schema::create('ws_users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('avatar')->nullable();
            $table->string('username')->nullable();
            $table->string('grade')->dafault('staff');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ws_users');
    }
};
