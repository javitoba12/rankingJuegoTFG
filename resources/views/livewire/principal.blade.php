
<div class='main-container d-flex flex-column flex-fill'>
 
  <div class="nav-container bg-dark">
  
    <nav class="navbar navbar-expand-lg {{ $tema['textColor'] }} {{ $tema['bgColor'] }}  {{ $tema['navbarColor'] }} my-nav">
    <div class='options-container mx-2'><button class="btn btn-danger  text-start" wire:click="cerrarSesion">Cerrar sesion</button></div>
    <div class='buscador-container mx-2 d-flex'> <?php //BUSCADOR PARA BUSCAR USUARIOS ?>
    
        <input type="text" class='mx-2 rounded' placeholder='Buscar usuario' name="" id="search1" wire:model='nickBusqueda'>
        <button class='btn btn-info' wire:click='buscarUsers':disabled='!$wire.nickBusqueda || !$wire.nickBusqueda.trim()'>Buscar</button> 
        {{-- disabled para evitar que el usuario introduzca espacios vacios --}}
        <button class='btn mx-2 btn-danger' wire:click='cancelarBusqueda'>Cancelar</button>
    </div>
    <div class="container justify-content-end">
    
        <ul class="navbar-nav">

        <li class="nav-item mx-5">
            <a class="nav-link" href="#"><h6>Monster Hunter</h6></a>
        </li>

        <li class="nav item">
            <a class="nav-link" href="https://steamcommunity.com/app/582010" target='_blank'>Foro</a>
        </li>

        <li class="nav-item">
        <a class="nav-link" href="/inicio/principal/perfil">Perfil</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/inicio/principal/inventario">Inventario</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/inicio/principal/bajas">Enemigos</a>
        </li>

        @if($usuario->rol == 'admin')
            <li class="nav-item">
                <a class="nav-link" href="/admin/inicio/principal/perfil/Administrar">Administracion</a>
            </li>
        @endif
        
        <!--  <li class="nav-item">
            <a class="nav-link disabled" href="#">Disabled</a>
        </li> -->
        </ul>
    </div>
    </nav>
</div> 



<div class="container mt-4 ranking-container {{ $tema['bgColor'] }} {{ $tema['textColor'] }} d-flex">


   <!-- <a href="/inicio/principal/perfil">Perfil</a>-->
   
   <!-- {{-- If your happiness depends on money, you will never be happy with yourself. --}}-->
    <div class='table-ranking-container d-flex flex-column'>
        <h1>Bienvenido {{$usuario->nick}}</h1>
        

        @if($usuario->rol == 'admin')

            <h4>Se ha registrado como administrador</h4>

        @endif

        @if(!empty($aviso))
        <div class="alert alert-danger"id="success" style="display:block;" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => 
        {show = false; $wire.borrarAviso();}, 3000)"> <p>{{$aviso}}</p>

</div>

        @endif


        <div class='select-ranking-container'>
            <select name="tipoRanking" id="tipoRanking" class='form-select form-select-sm text-light bg-secondary'  wire:change="seleccionRanking" wire:model="tipo">
                <option value="diezMejores">Diez mejores puntuaciones</option>
                <option value="personal">Ranking Personal</option>
                <option value="rankingBajas">Diez usuarios con mas bajas</option>
            </select>
        <!-- <button class="btn btn-danger" wire:click="cerrarSesion">Cerrar sesion</button> -->
        </div>

        @if(empty($aviso))

        

       
            @if(session()->has('usuariosCoincidentes'))
                <div class='bg-dark pb-2 pt-2 mb-2 rounded-3 w-100 h-50 d-flex flex-column'>
                <h4 class='mx-2'>Usuarios encontrados:</h4>
                @if(count(session()->get('usuariosCoincidentes'))==1)

                    <button class='btn btn-info mx-2 w-25' wire:click='seleccionarUsuario({{session()->get("usuariosCoincidentes")[0]}})' >{{session()->get('usuariosCoincidentes')[0]->nick}}</button>

                @elseif(count(session()->get('usuariosCoincidentes'))>1)

                        @foreach(session()->get('usuariosCoincidentes') as $usuario)

                            <button class='btn btn-info mb-2 w-25 mx-2' wire:click='seleccionarUsuario({{$usuario}})'>{{$usuario->nick}}</button>

                        @endforeach
                @endif
                </div>
            @elseif(isset($ranking) && count($ranking) > 0)

            
                @if($tipo == 'diezMejores' || $tipo == 'personal')<?php //Si ranking contiene informacion 
                // sobre los diez mejores o el ranking personal, creare una tabla ?>
                
                <div class='d-flex flex-row flex-between'>

                      @if($usuarioSeleccionado->id!=$usuario->id)
                        <p class='mt-3 mx-2'>Usuario: {{$usuarioSeleccionado->nick}}</p>
                        
                            <button wire:click='verPerfilSeleccionado' class='btn btn-info mt-1'>Ver perfil</button>
                        @endif
                </div>
                    <table class="table table-hover {{ $tema['tableColor'] }} mt-3">
                        <thead>


                            @foreach(array_keys($ranking[0]->getAttributes()) as $columna)
                            <?php
                            /*Similar a Object.keys en js cuando hago un for of, aqui del mismo modo, me llevo 
                            los nombres de los atributos(con atributos me refiero a los nombres de las columnas de 
                            la tabla en la BD) del primer objeto de la coleccion, (ya que todos los objetos tienen los 
                            mismos atributos) para recorrerlos con un foreach 
                            
                            getAttributes es una funcion que ofrece eloquent, no es nativa de php*/ 
                            ?>

                            @if($columna!='id' && $columna!='user_id' && $columna!='mission_id' && $columna!='Created_ad' && $columna!='Updated_at')
                                <th class='table-warning'>{{ ucfirst(str_replace('_', ' ', $columna)) }}</th>
                                <?php 
                                /*A la hora de imprimir los nombres de las columnas, uso El metodo ucfirst, que
                                convierte la primera letra de una palabra en string, dentro de ucfirst llamo antes a la
                                funcion replace, la cual cambia los _ de los nombres, por espacios reales entre palabras*/
                                ?>
                            @endif
                            @endforeach
                        </thead>
                        <tbody>

                            <?php //En funcion de la opcion escogida por el usuario, llenare
                            // el tbody con informacion del ranking global o informacion sobre el ranking
                            // personal ?>

                            @if($tipo == 'diezMejores')
                                @foreach($ranking as $jugador)
                                    <tr>
                                        <td>{{$jugador->nick}}</td>
                                        <td>{{$jugador->puntuacion_total}} pts</td>
                                    </tr>
                                @endforeach
                            @elseif($tipo == 'personal')

                                @foreach($ranking as $puntuacion)
                                    <tr>
                                        <td>Mision: {{$puntuacion->nombre}}</td>
                                        <td>{{$puntuacion->puntuacion}} pts</td>
                                        <td>{{$puntuacion->marca_tiempo}} min</td>
                                        <td style='display:none;'>{{$puntuacion->id}}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                @else <?php //En caso de que el usuario elija ver los diez jugadores con mas bajas 
                // (tuve que separar esta opcion de las otras dos, por problemas con livewire al intentar
                // ejecutar consultas de dos modelos distintos para pintar su informacion sobre una misma tabla) ?>
                    <table class="table table-hover table-dark mt-3">
                            <thead>
                                <tr>
                                    <th class='table-warning'>Usuario</th>
                                    <th class='table-warning'>Bajas totales</th>
                                </tr>
                            </thead>
                            @foreach($ranking as $puntuacion)
                            <tbody>
                                <tr>
                                    <td>{{$puntuacion->nick}}</td>
                                    <td>{{$puntuacion->bajas_totales}}</td>
                                </tr>
                            </tbody>
                            @endforeach
                        
                    </table>
                @endif
           
           
            @endif

            

        
    </div>
    

    <!-- Gráfico de Livewire Charts -->

    <div class="my-4" style="width: 50%; max-width: 500px; height: 50vh; margin: 0 auto;">

    @if(!empty($chartModel) && !session()->has('usuariosCoincidentes'))
        @if($tipo == 'personal')
            
                        <livewire:livewire-pie-chart
                :pie-chart-model="$chartModel"
                :key="'personal-'.$usuarioSeleccionado->id"
            />
            

        @elseif($tipo == 'diezMejores')
            <livewire:livewire-column-chart :column-chart-model="$chartModel" />
        @else
            <livewire:livewire-column-chart :column-chart-model="$chartModel" />
        @endif
    @endif
    </div>
@endif
    
    
</div>

  <script>
     window.addEventListener('recargarPagina', function () {//Creo un evento asociado a la pagina actual

        window.location.reload();//Cuando este evento se dispare, provocara que se recargue la pagina actual

     });
  </script>  

</div>


