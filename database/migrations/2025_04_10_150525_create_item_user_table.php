<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void //RELACION 1-1 (Un usuario solo posee un inventario)
    {//ESTA ES LA TABLA INVENTARIO, llamada item_user para aprovechar que encaje con el estandar de laravel
    //sin necesidad de mas configuraciones, y se aproveche mejor el funcionamiento de Eloquent

        Schema::create('item_user', function (Blueprint $table) {
           // $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->timestamps();//Usare este campo para saber en que momento consiguio el usuario
            //el item y cuando consiguio el ultimo item de un tipo si aumenta la cantidad de dicho item.

            $table->primary(['user_id', 'item_id']);//Estos dos campos actuaran como clave primaria
            //de la tabla 
            // Para evitar que la combinacion de usuario e item
            //se repita mas de una vez, en lugar de ello para indicar si el usuario tiene un mismo objeto
            //varias veces, utilizaremos la celda cantidad

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_user');
    }
};
