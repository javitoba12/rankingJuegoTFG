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
        Schema::create('enemigo_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            //$table->foreignId('enemigo_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('enemigo_api_id');
            $table->integer('numero_bajas')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'enemigo_api_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enemigo_users');
    }
};
