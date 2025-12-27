<div class='bajas-page'>
    {{-- Stop trying to control. --}}
    <div class='enemigosContainer'>
        @if(isset($bajas) && count($bajas) > 0)

        <h2>Recuento de bajas a enemigos:</h2>

        @if(session()->has('aviso'))
            <h4>{{session('aviso')}}</h4>
        @endif

        
        @foreach($bajas as $baja)
        <ul>
            <li>
               <b>Nombre: {{$baja['nombre_enemigo']}}</b>
            </li>
            <li>
                tipo de monstruo: {{$baja['tipo_monstruo']}} 
            </li>
            <li>
               especie: {{$baja['especie']}}
            </li>
            <li>
               bajas: {{$baja['numero_bajas']}}
            </li>
            <li><button class='btn btn-warning' value='{{$baja["enemigoId"]}}' wire:click='detalles({{$baja["enemigoId"]}})'>Detalle</button></li>
             <?php //Este boton llama a una funcion que redirige al usuario a una vista, donde se muestran los 
             // detalles de los enemigos ?>
        </ul>
        @endforeach

        @else

            <p>Aun no has vencido a ningun enemigo</p>

        @endif

        
    </div>
    <div class='opcionesBtn'>
            <button class='btn btn-success' wire:click='importarBajas'>Importar Bajas</button>
            <?php // boton con el que se actualiza o insertan nuevas tablas ?>
            
            <button class='btn btn-info' wire:click='volver'>Volver a Principal</button>
        </div>
</div>
