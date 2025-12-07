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
        Schema::create('saved_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre_mision');
            $table->string('nombre_partida');
            $table->text('estado_personaje');//vida,energia,etc.
            $table->timestamp('fecha_guardado');
            $table->unique(['user_id', 'nombre_partida']);
            //Limito a que no se pueda repetir la combinacion de usuario y nombre de partida
            //ej: user1 -> saved1 no podra repetirse de nuevo, en lugar de eso debera crearse
            //otra nueva partida: user1 -> saved2 por ejemplo,
            //la idea es que si el usuario vuelve a escribir saved1 y ya existe una partida
            //con ese nombre, dicha partida se sobreescriba.

            //Eso si, si se permite que user2 o user3 por ejemplo tenga una partida llamada tambien
            //saved1, lo que no se puede repetir de nuevo es la combinacion de usuario y
            //nombre de la partida.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_games');
    }
};
