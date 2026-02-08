<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Mission;
use App\Models\MissionUser;
use App\Models\EnemigoUser;
use App\Http\Traits\colorTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;//Para manejo de sesiones
use Illuminate\Support\Facades\DB;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;//Para el manejo de graficas
use Asantibanez\LivewireCharts\Models\PieChartModel;//Para el manejo de graficas tipo pastel


class Principal extends Component
{

    use colorTrait;

    public $usuario;
    public $ranking;
    public $tipo;
    public $aviso;
    public $nickBusqueda;
    public $usuarioSeleccionado;
    public $tema;
    public $isPintar=false;
    public $charModel;


    public function mount(){//mount en livewire es similar a usar un constructor en php,
        //todo lo que se defina dentro de esta funcion, se ejecuta automaticamente al cargar la
        //vista correspondiente al componente actual
        if(Auth::check()){//Con esto compruebo si existe un usuario en la sesion, y de ser ese el caso...

            $this->usuario=Auth::user();//Asigno a una variable local, los datos del usuario de la sesion
            
            $this->comprobarUsuarioBuscado();

            if(session()->has('aviso')){
                $this->aviso=session()->get('aviso');
                session()->forget('aviso');
            }
            $this->tema=$this->aplicarColor();
            $this->comprobarRankingEnSesion();
           

    }else{
        return redirect()->route('inicio');
    }
       
    }


    private function comprobarUsuarioBuscado(){

        if(session()->has('usuarioBuscado')){//Si existe un usuario buscado almacenado en la sesion...
                
                $this->usuarioSeleccionado=User::buscarUsuario(session()->get('usuarioBuscado'));//Extraigo su informacion de la BD
                //y lo guardo en una variable local

                session()->forget('usuarioBuscado');//Borro al usuario de la sesion, para evitar que persista en caso de que el usuario
                //logueado quiera cambiar de ranking o navegar por la web

                //session()->forget('aviso');

                //No puedo hacerlo en render, puesto que seria demasiado tarde borrar al usuario una vez retornada la pagina renderizada.
                //Por lo tanto el usuario persistira en la sesion permanentemente, y si lo hago antes de retornar la busqueda, los
                //datos del usuario se perderan antes de poder imprimirlos en la pagina.

                //Por ello lo guardo en una variable local justo cuando el componente de principal empieza a cargar los datos para la vista
                //por lo que los datos se imprimiran en el actual render, luego lo borro de la session, para que en el proximo render
                //los datos del usuario buscado no existan, y causen confusion en el usuario logueado cuando visualice sus rankings.
            }else{
                $this->usuarioSeleccionado=$this->usuario;
                
            }

    }

    private function comprobarRankingEnSesion(){

        $this->isPintar=true;

        if(!session()->has('tipo')){//Si aun no existe ninguna opcion de tipo de ranking escogido por el
            //usuario en la sesion...

            $this->tipo='diezMejores';//Por defecto tipo pasara a valer diezMejores
            //indicando que por defecto se mostraran las 10 mejores puntuaciones globales del
            //ranking
           // $this->ranking=MissionUser::getDiezMejores();
            //Extraigo en una consulta las diez mejores puntuaciones, y la coleccion de filas
            //devueltas por la BD, pasaran a almacenarse en ranking, para asi recorrer y mostrar
            //las puntuaciones mas tarde
            
        }else{
            $this->tipo = session()->get('tipo');// esto es igual a $this->tipo=$_SESSION['tipo']
            
        }

        $this->seleccionRanking();
    }

    public function borrarAvisos(){
        session()->forget('aviso');
    }

    public function seleccionRanking(){

        $this->borrarAvisos();
        $this->aviso='';
    


        if($this->tipo!='personal' && $this->usuarioSeleccionado!=$this->usuario){
            $this->usuarioSeleccionado=$this->usuario;//Para evitar que la opcion de ver perfil del usuario salga, si se busco un usuario anteriormente antes de
            //cambiar de ranking
        }

        
        
        if($this->tipo =='personal'){//Si el usuario elije la opcion personal 
            //de select...
        
        $this->ranking=MissionUser::getMisionesUser($this->usuarioSeleccionado->id);
        //Extraigo en una consulta todas las puntuaciones de las misiones
        //asociadas al usuario(aquellas filas de mission_users que posean el id del usuario)

       

        if(empty($this->ranking) || count($this->ranking)<=0){//Si la consulta me devuelve una coleccion vacia

            $this->aviso='Aun no has completado ninguna mision';//aviso al usuario
            //session()->flash('aviso','Aun no has completado ninguna mision');
        }



        


    }elseif($this->tipo == 'diezMejores'){//Si el usuario ha elegido como tipo de ranking
        //las 10 mejores puntuaciones...

        $this->ranking=MissionUser::getDiezMejores();//Extraigo a los 10 mejores

        

        if(empty($this->ranking) || count($this->ranking)<=0){//Si la consulta me devuelve una coleccion vacia
            $this->aviso='Aun no hay puntuaciones globales disponibles';
           //session()->flash('aviso','Aun no hay puntuaciones globales disponibles');
            //aviso al usuario
        }

        

    }elseif($this->tipo == 'rankingBajas'){
        $this->ranking=EnemigoUser::getDiezUsuariosConMasBajas();


        if(!isset($this->ranking) || count($this->ranking)<=0){//Si la consulta me devuelve una coleccion vacia
            $this->aviso='Aun no hay bajas globales disponibles';
            //session()->flash('aviso','Aun no hay bajas globales disponibles');
            //aviso al usuario
        }


       
    }

    session()->put('tipo',$this->tipo);//guardo el tipo de ranking en la sesion
    $this->chartModel=$this->pintarGrafico();
    
}




private function pintarGrafico(){

     $chart=null;//con esto evito errores

        if ($this->ranking && $this->ranking->isNotEmpty()) {

            if($this->tipo == 'personal'){

                $chart = (new PieChartModel())//Creo un nuevo grafico que guardo en chart, y que se imprimira en el momento en el que
            //se renderice la vista 

            ->setTitle('Ranking: ' . ucfirst($this->tipo))
            ->setAnimated(true)//Habilito animaciones para mas dinamismo al grafico
            ->setDataLabelsEnabled(true)
            ->setLegendVisibility(false);//Desactivo la leyenda e informacion mas redundante

            $total=0;
            foreach ($this->ranking as $fila) {//recorro toda la informacion con un bucle
                    //$chart->addColumn($fila->nombre, $fila->puntuacion, '#f87171');
                    $total= $total + $fila->puntuacion; 
                    
                    
            }

            $chart->addSlice('Puntuacion total',$total,'#f87171');
            $chart->addSlice('Horas jugadas',$this->usuarioSeleccionado->tiempo_juego,'#60a5fa');

            

            }else{

            

                    $chart = (new ColumnChartModel())//Creo un nuevo grafico que guardo en chart, y que se imprimira en el momento en el que
                //se renderice la vista 

                ->setTitle('Ranking: ' . ucfirst($this->tipo))
                ->setAnimated(true)//Habilito animaciones para mas dinamismo al grafico
                ->setDataLabelsEnabled(true)
                ->setColumnWidth(60)
                ->setLegendVisibility(false);//Desactivo la leyenda e informacion mas redundante


                if ($this->tipo == 'diezMejores') {//En caso seleccionar diez mejores...
                    foreach ($this->ranking as $fila) {
                        $chart->addColumn($fila->nick, $fila->puntuacion_total, '#60a5fa');
                        //Repito el mismo proceso, pero el titulo se cambia por el nick, y la puntuacion que se muestra
                        //es la puntuacion total del jugador
                    }
                } elseif ($this->tipo == 'rankingBajas') {
                    foreach ($this->ranking as $fila) {
                        $chart->addColumn($fila->nick, $fila->bajas_totales, '#34d399');
                        //Igual que las otras dos anteriores, pero adaptado a la tematica de enemigos vencidos
                    }
                }

            }

        
    }

return $chart;

}



function buscarUsers(){

    if(empty(trim($this->nickBusqueda)) && trim($this->nickBusqueda)!= ''){

       session()->flash('aviso','El campo de búsqueda está vacío');

    }else{
    $usuariosCoincidentes=User::buscarUsuariosCoincidentes($this->nickBusqueda);

    if(empty($usuariosCoincidentes) || $usuariosCoincidentes == null || $usuariosCoincidentes->count()<=0){

                        $this->aviso='No se ha encontrado ningun usuario';
                        $this->tipo='diezMejores';
                        $this->seleccionRanking();

                    }else{

                        session()->flash('usuariosCoincidentes',$usuariosCoincidentes);

                    }
    }

}

function seleccionarUsuario($usuarioBuscado){

    if(!empty($usuarioBuscado)){
        
        $this->usuarioSeleccionado=User::buscarUsuario($usuarioBuscado);
        $this->tipo='personal';
        $this->seleccionRanking();
        session()->put('tipo', 'personal');
    }else{
        return redirect()->route('principal');
    }

}



function cancelarBusqueda(){

    if(!empty(trim($this->nickBusqueda)) || $this->usuarioSeleccionado->id != $this->usuario->id){

        session()->forget('usuarioBuscado');
        $this->seleccionRanking();
        //$this->dispatch('recargarPagina');
        return redirect()->route('principal');

    }else{
        $this->seleccionRanking();
    }
}

public function verPerfilSeleccionado(){
    if($this->usuarioSeleccionado->id!=$this->usuario->id){

        session()->put('perfilSeleccionado',$this->usuarioSeleccionado->id);
        return redirect()->route('perfil');

    }

}


public function cerrarSesion()
{
    Auth::logout();// Cierro la sesión del usuario,(equivalente a unset($_SESSION['usuario']))
    session()->flush();// Elimino los datos de la sesión
    session()->regenerateToken();//Para evitar ataques por CSFR
    return redirect()->route('inicio');// Redirijo al usuario al inicio de la web
}


   public function render()
{

   
    
    
     

    return view('livewire.principal', [
        'chartModel' => $this->pintarGrafico()
    ]);// Paso el modelo del gráfico a la vista, donde será interpretado por el componente Livewire Chart en la vista de principal.
    // El componente <livewire:livewire-column-chart> importado a mi vista principal
// se encargara de procesar la información del modelo ($chart) y renderizar el gráfico

    
}
    
}

