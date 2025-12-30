<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Item;
use App\Models\Enemigo;
use App\Models\EnemigoUser;
use Illuminate\Support\Facades\Http;

class Detalle extends Component
{
    public $idSeleccionado;
    public $isItem;
    public $informacionExtraida;
    public $paginaOrigen;

    public function mount(){

        if(session()->has('idSeleccionado') && session()->has('isItem')){//Compruebo que la sesion contenga
            //el id del item o enemigo y el booleano de isItem, para saber como debo actuar

            //Guardo ambos datos en atributos de livewire
            $this->idSeleccionado=session()->get('idSeleccionado');
            $this->isItem=session()->get('isItem');
            $this->recogerDetalles();
        }else{//Si no hay datos en la sesion, llevo al usuario a principal
            return redirect()->route('principal');
        }

    }

    public function recogerDetalles(){
        if($this->isItem){//Si debo mostrar el detalle de un item...

            $this->informacionExtraida=Item::buscarItemPorId($this->idSeleccionado);
            //busco y extraigo toda la informacion del item seleccionado en la tabla items
            $this->paginaOrigen='inventario';//declaro una variable con el alias de la pagina origen
            //en ambos casos, para el momento en el que el usuario quiera volver a la pagina anterior

        }else{
            //$this->informacionExtraida=Enemigo::detalleEnemigo($this->idSeleccionado);
            $this->informacionExtraida=collect(Http::get('https://mhw-db.com/monsters/' . $this->idSeleccionado)->json());
            //busco y extraigo toda la informacion del enemigo seleccionado en la tabla enemigos
            $this->paginaOrigen='bajas';
        }
    }


    public function volver(){//Si el usuario selecciona volver
        //Borro los datos sobre el item o enemigo de la session
        session()->forget('idSeleccionado');
        session()->forget('isItem');
        return redirect()->route($this->paginaOrigen);//redirijo al usuario a su respectiva pagina 
        //de origen
    }

    public function render()
    {
        return view('livewire.detalle');
    }
}
