<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Inventario extends Component
{

    public $usuario;
    public $items;
    public $aviso;
    public $idSeleccionado;

    public function mount(){
        if(Auth::check()){
            $this->usuario=Auth::user();
            $this->recuperarInventario();
            
            if(session()->has('aviso')){
                $this->aviso=session()->get('aviso');
            }
        }else{
            return redirect()->route('inicio');
        }
    }

    public function recuperarInventario(){
       $this->items = User::find($this->usuario->id)->items;
       //Con esto accedo a todos los items relacionados con el usuario
       //actual logueado en la pagina, gracias a la funcion items() que he definido
       //en el modelo User, que se encarga de crear la relacion N-N User/Item por la parte del usuario

       //Mencion importante User::find($this->usuario->id)->items , devuelve una coleccion de items
       //User::find($this->usuario->id)->items() devuelve una instancia de items o mas bien 
       // a la relación N-N User/Item como un objeto.
       //Esto puede ser util para el uso de consultas, por ejemplo:
       /*
       User::find($this->usuario->id)->items() serai algo parecido a hacer esto en sql:

       SELECT items.*, item_user.cantidad, item_user.created_at, item_user.updated_at
        FROM items
        INNER JOIN item_user ON items.id = item_user.item_id
        WHERE item_user.user_id = id;


        $items = $relacion->where('efecto', 'curativo')->withPivot('cantidad')->get();
       */


       if(empty($this->items) || count($this->items)<=0){
            $this->aviso='Aun no has conseguido ningun item';
       }
    }

    public function importarInventario(){
       
        $itemsDisponibles=DB::table('items')->pluck('id');
        //Extraigo la id de todos los items registrados en la tabla items

        foreach($itemsDisponibles as $itemSeleccionado){
            //Recorro todos los items de la tabla items
           

            $nuevaCantidad=rand(0,5);//Genero una nueva cantidad para el item actual, comprendida entre 0
            //y 5

            $itemAdquirido=$this->usuario->items()->where('item_id',$itemSeleccionado)->first();
            //Hago una consulta a item_user ya que estoy usuando los parentesis junto a items
            //Lo cual quiere decir que esto NO devuelve una coleccion de los items asociados al usuario
            //segun que requisitos, si no que estoy preguntando en la tabla item_users que me devuelva
            //la fila con la que coincidan el id del usuario logueado, y el id del item actual 
            //sobre el que estoy iterando, si existe una fila con la combinacion actual user_id/item_id,
            //en $itemAdquirido se almacena un objeto con la informacion de la fila

            /*Usuario->items() a secas y con () en items es el equivalente a hacer
            
            SELECT items.*, item_user.cantidad, item_user.created_at, item_user.updated_at
            FROM items
            INNER JOIN item_user ON items.id = item_user.item_id
            WHERE item_user.user_id = 1;
            
            al que luego le añadimos otro where para el campo item_id e indicamos que nos devuelva
            solo el primer resultado, para evitar que nos devuelve una coleccion de una sola fila*/

            if(!empty($itemAdquirido)){//Si la fila existe...

                if($nuevaCantidad>0){//Compruebo que la cantidad generada es mayor que 0
                    
                    $this->usuario->items()->updateExistingPivot($itemAdquirido->id, ['cantidad' => $nuevaCantidad]);
                    //Si es mayor que 0, actualizo la fila y con la la nueva cantidad

                    //Esto es como hacer un UPDATE item_user SET cantidad=':cantida'
                    //where use_id=:user_id AND item_id=:item_id
                
                }else{//En caso de que la cantidad sea 0...

                    $this->usuario->items()->detach($itemAdquirido->id);//Borro la fila, simulando
                    //que el usuario ha gastado todos los items almacenados relacionados con el
                    //id del item (O lo que es igual, ha gastado todos los items de esa categoria
                    //que tenia en su inventario)

                    //detach es el equivalente a hacer un delete from item_user where item_id=':item_id' AND 
                    // user_id =:user_id
                }

                

            }else{//Si el item no estaba asociado al usuario en la tabla item_user...

                if($nuevaCantidad>0){//Y ademas la cantidad generada es mayor a 0...
                    $this->usuario->items()->attach($itemSeleccionado, ['cantidad' => $nuevaCantidad]);
                    //Introduzco una nueva fila donde asocio user_id e item_id en la tabla item_user
                    //Idicando que el usuario ha conseguido uno o varios items nuevos que no figuraban
                    //en su inventario

                    //Attach es similar a usar insert_into(campos) values(valores)
                    
                }
            }

           
        }

        session()->flash('aviso','El inventario se ha importado correctamente.');
        //Aviso al usuario del exito 
        return redirect()->route('inventario');//Refresco la pagina
    }


    public function volver(){
        //session()->forget('aviso');
        return redirect()->route('principal');
    }

    public function detalles($idSeleccionado){
        session()->put('idSeleccionado',$idSeleccionado);
        session()->put('isItem',true);
        return redirect()->route('detalle');
    }

    public function render()
    {
        return view('livewire.inventario');
    }
}
