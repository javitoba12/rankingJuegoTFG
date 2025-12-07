<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Enemigo extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [//Los campos que permito al usuario rellenar para la tabla usuarios en este caso
        'nombre_enemigo',
        'descripcion',
        'debilidades',
        'daño',
        'tipo_daño'
    ];


    protected $hidden = [
        'remember_token',
    ];

    public function users(){
        return $this->belongsToMany(User::class,'enemigo_users')
        ->withPivot('numero_bajas')
        ->using(EnemigoUser::class);
    }


    public static function contarEnemigos(){
        return self::count();//Esto no devuelve una fila, devuelve un numero
     }


     public static function detalleEnemigo($idEnemigo){

       return self::find($idEnemigo);
     }
}
