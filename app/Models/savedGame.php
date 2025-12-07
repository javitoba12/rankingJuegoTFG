<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class savedGame extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [//Los campos que permito al usuario rellenar para la tabla usuarios en este caso
        'user_id',
        'nombre_mision',
        'nombre_partida',
        'estado_personaje',
        'fecha_guardado'
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function user(){
    return $this->belongsTo(User::class);
}

    public static function obtenerPartidasUsuario($idUsuario){

        return self::where('user_id',$idUsuario)->get();

    }
}
