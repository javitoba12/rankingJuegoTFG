<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Item extends Model
{

    use HasFactory, Notifiable;
    
    protected $fillable = [//Los campos que permito al usuario rellenar para la tabla usuarios en este caso
        'nombre',
        'descripcion',
        'efecto',
        'magnitud',
        'duracion',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public static function recogerTodosLosItems(){
       return self::all();
    }

    public static function buscarItemPorId($id){
        return self::find($id);//Hago una consulta que me devuelve de la tabla items, aquella fila
        //que coincida con el id introducido por parametro, esto devuelve todos los campos de la fila
    }

    public function users(){
        return $this->belongsToMany(User::class)->withPivot('cantidad')
        //Aqui especifico a la tabla que no solo me traiga los campos que componen el id de cada fila
        //si no que ademas tambien rescate el campo de cantidad en cada fila


        ->withTimestamps();//Con esto tambien consigo rescatar los campos con fecha de created_ad y
        //updated_at para mas informacion adicional, ademas de conseguir que laravel los gestione automa
        //ticamente en cada fila, cuando se produzca un insert o un update(en este caso se modificaria
        //el campo update_at)
    }

    public static function contarItems(){
        return self::count();//Esto no devuelve una fila, devuelve un numero
     }
}
