<?php

namespace App\Http\Traits;//Esta es la ruta que defino, y con la que luego puedo buscar e importar esta clase en otros componentes o clases.
use Illuminate\Support\Facades\Auth;


trait colorTrait {

    public function aplicarColor(){
        $tema;
        $colorDefecto='oscuro';
        

        if(Auth::check()){
            $usuario=Auth::user();
            

            $tema = [//Si el usuario tiene configurado el tema por defecto (oscuro), en cada celda del array relacionada con los estilos, declaro como valores
                //todos los estilos que tiene el tema oscuro de mi web, en caso contrario signifca que el usuario tiene configurado el tema claro, por lo cual paso
                //a declarar como valores, todos los estilos relacionados con el tema claro de mi web
                'bgColor'   => $usuario->tema == $colorDefecto ? 'bg-dark' : 'container-claro',
                'textColor' => $usuario->tema == $colorDefecto ? 'text-white' : 'text-dark',
                'navbarColor' => $usuario->tema == $colorDefecto ? 'navbar-dark' : 'navbar-light',
                'tableColor' => $usuario->tema == $colorDefecto ? 'table-dark' : 'table-light'
            ];
        }else{//En caso de que el usuario no este logueado, se aplica el tema oscuro, por si hay algunas zonas en la web donde se permita al usuario que pueda acceder sin 
        // loguearse
            $tema = [
                'bgColor'   =>  'bg-dark',
                'textColor' =>  'text-white',
                'navbarColor' =>  'navbar-dark',
                'tableColor' => 'table-dark'
                ];
        }
    

        return $tema;
    }
}


?>