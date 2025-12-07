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
        Schema::create('enemigos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_enemigo');
            $table->text('descripcion');
            $table->string('debilidades');
            $table->integer('daño');
            $table->string('tipo_daño');
            $table->rememberToken();
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('enemigos');
    }
};
