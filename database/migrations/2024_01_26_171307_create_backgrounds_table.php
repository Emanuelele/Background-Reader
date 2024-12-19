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
        Schema::create('backgrounds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('generality');
            $table->string('link');
            $table->string('type');
            $table->string('discord_id');
            $table->string('steam_hex');
            $table->string('note')->nullable();
            $table->string('reader')->nullable();
            $table->boolean('haspriority')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backgrounds');
    }
};
