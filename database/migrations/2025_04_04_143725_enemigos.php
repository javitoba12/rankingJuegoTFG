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
            $table->unsignedBigInteger('enemigo_api_id');
            $table->string('nombre_enemigo');
            $table->string('tipo_monstruo');
            $table->string('especie');
           // $table->text('descripcion');
         //   $table->string('debilidades');
          //  $table->integer('daño');
           // $table->string('tipo_daño');
            $table->rememberToken();
            $table->timestamps();
            $table->unique('enemigo_api_id');
        
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
