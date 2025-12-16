
<div>

<!--Barra de navegacion-->

<div class="nav-container">
  
  <nav class="navbar navbar-expand-sm bg-light navbar-light bg-dark navbar-dark ">
  <div class='options-container d-flex'>
    <button class="btn btn-success  text-start" wire:click="crearUser">Crear nuevo Usuario</button>
    <button class="btn btn-success  text-start" wire:click='mostrarFormEditar("","busqueda")'>Buscar usuario</button>
</div>
  <div class="container-fluid justify-content-end">
  
      <ul class="navbar-nav">

      <li class="nav-item mx-5">
          <a class="nav-link" href="#"><h6>Project Dracokeos</h6></a>
      </li>

      <li class="nav-item">
      <a class="nav-link" href="/inicio/principal/perfil">Perfil</a>
      </li>
      <!--<li class="nav-item">
          <a class="nav-link" href="/inicio/principal/inventario">Inventario</a>
      </li>-->

      <li class="nav-item">
          <a class="nav-link" href="/inicio/principal">Principal</a>
      </li>

      <!--<li class="nav-item">
          <a class="nav-link" href="#">Enemigos</a>
      </li>-->

     
      
      <!--  <li class="nav-item">
          <a class="nav-link disabled" href="#">Disabled</a>
      </li> -->
      </ul>
  </div>
  </nav>
</div> 




<div class='d-flex flex-column justify-content-center align-items-center'>
    
    <h1 class=''>Gestion de usuarios</h1>

    @if(session()->has('aviso'))
        <div class="alert alert-success" id="success" style="display:block;">
            <p>{{session('aviso')}}</p>
        </div>
    @endif

    @if(session()->has('accion'))
        <div class="alert alert-success" id="success" style="display:block;">
            <p>{{session('accion')}}</p>
        </div>
    @endif


    <?php // Formularios para modificar usuario e inventario ?>

    @if($editar && $tipoEdicion == 'busqueda')

        <div class='mb-2 buscadorAdmin pt-2 pb-2 px-2 bg-dark rounded-3'>
            <input type="text" name="" class='rounded' id="nickBuscado" wire:model='nickBusqueda' placeholder='Introduzca un nick'>
            <button class='btn btn-info' wire:click='buscarUser'>Buscar</button>
            <button type='button' class='btn btn-danger' wire:click='refrescar'>Cancelar</button>
        </div>

    @endif

    @if($editar && $tipoEdicion=='eliminarUser')

        <div class='editUser mb-2 bg-dark pt-2 pb-2'>
            <h4>¿Esta seguro de que desea eliminar al usuario?</h4>
            <button wire:click='EliminarUser({{$usuarioSeleccionado->id}})' class='btn btn-danger'>Confirmar</button>
            <button wire:click='refrescar' class='btn btn-success'>Cancelar</button>
        </div>

    @endif

    @if($editar && $tipoEdicion == 'usuario')

        
        <p class='bg-dark px-4 rounded-3'>Editar usuario {{$usuarioSeleccionado->nick}}</p>
        <form class='editUser mb-2 bg-dark pt-2' wire:submit='actualizarUser'>
            <div class='nombreContainer'>
            <label for="nombre">Nuevo nombre:</label>
            <input type="text" name="" id="nombre" value='{{$usuarioSeleccionado->nick}}' wire:model='nuevoNick'>
            </div>
            <div class='claveContainer'>
            <label for="clave">Nueva clave:</label>
            <input type="password" name="" id="clave" value='' wire:model='nuevaClave'>
            </div>
            <div class='btonForm'>
            <button type='submit' class='btn btn-success'>Guardar</button>
            <button type='' class='btn btn-info' wire:click='refrescar'>Cancelar</button>
            </div>

            @if($errors->any())
                <?php
                /*Si el array $errors (array que laravel 11 proporciona de manera 
                automatica y comoda) contiene algun error en el momento de ejecutar el formulario de
                login...

                Hago un bucle foreach y creo tantos div alert de botstrap como errores 
                contenga $errors*/
                ?>
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger" style="display:block ;">
                            <strong>Error:</strong> {{ $error }}
                        <?php /*Pinto cada error del array en un alert diferente*/ ?>
                        </div>
                    @endforeach
                @endif
        </form>


    <?php //Formulario para inventario ?>

    @elseif($editar && $tipoEdicion == 'inventario')

        @if(isset($itemsDisponibles))
        <form wire:submit='modificarInventario' class='editUser mb-2 bg-dark pt-2 pb-2'>
            <label for="accionInventario">Accion:</label>
            <select name="" id="accionInventario" wire:model='accionInventario'>
                <option value="" selected disabled>Selecciona una accion</option>
                <option value="aniadir">Añadir un Objeto</option>
                <option value="actualizar">Actualizar la canitidad de un objeto</option>
                <option value="eliminar">Eliminar un objeto</option>
            </select>
            <label for="items">Item:</label>
            <select name="" id="items"  wire:model='idItem'>
                <option value="" selected disabled>Selecciona un item</option>
                @foreach($itemsDisponibles as $indice => $id)

                    <option value={{$id}}>{{$indice}}</option>

                @endforeach
            </select>

            <label for="cantidad">Nueva Cantidad:</label>
            <input type="number" name="" id="cantidad" wire:model='cantidadItem'>

            <div class='btonForm'>
                <button type='submit' class='btn btn-success'>Guardar</button>
                <button type='reset' class='btn btn-info' wire:click='refrescar'>Cancelar</button>
                <button type='reset' class='btn btn-info' wire:click='verInventarioSeleccionado'>Ver Inventario</button>
            </div>
        </form>

        

        @endif
    
    @endif

   <?php // Informacion del usuario encontrado ?>

    
    @if(session()->has('usuarioBuscado'))
    <h6>Resultado:</h6>
    <div class='bg-dark pb-2 pt-2 mb-2 rounded'>
        <table class='table-bordered mb-2 mt-1 bg-dark'>
            <tbody>
                <tr>
                    <td>{{session('usuarioBuscado')->nick}}</td>
                    <td>{{session('usuarioBuscado')->email}}</td>
                    <td>{{session('usuarioBuscado')->fecha_alta}}</td>
                    <td>{{session('usuarioBuscado')->tiempo_juego}} horas jugadas</td>
                    <td>{{session('usuarioBuscado')->rol}}</td>
                    
                    @if(session('usuarioBuscado')->rol=='usuario')

                    <?php /*Solo doy opciones de manipular los datos de los usuarios que no tienen rol
                    de admin*/ ?>
                        <td>
                            <button class='btn btn-info' value='{{session("usuarioBuscado")->id}}' wire:click='mostrarFormEditar({{session("usuarioBuscado")->id}},"usuario")'>Actualizar Usuario</button>
                            <button value='{{session("usuarioBuscado")->id}}' wire:click='mostrarMenuBorrarUser("eliminarUser",{{session("usuarioBuscado")->id}})' class='btn btn-danger'>Eliminar usuario</button>
                            <button value='{{session("usuarioBuscado")->id}}' wire:click='mostrarFormEditar({{session("usuarioBuscado")->id}},"inventario")' class='btn btn-info'>Modificar inventario</button>
                            <button value='{{session("usuarioBuscado")->id}}' wire:click='reiniciarEstadisticas({{session("usuarioBuscado")->id}})' class='btn btn-danger'>Reiniciar estadisticas</button>
                        </td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
    @endif


    <?php //Tabla para mostrar todos los usuarios de la BD paginados ?>

    <div class='adminInformacionContainer mb-2 bg-dark d-flex flex-column justify-content-center align-self-center'>
    @if(isset($usuarios) && $paginacion)
        <table class='table-bordered'>
            <thead>
            @foreach(array_keys($usuarios[0]->getAttributes()) as $campoNombre)
                <?php
                /*Similar a Object.keys en js cuando hago un for of, aqui del mismo modo, me llevo 
                los nombres de los atributos(con atributos me refiero a los nombres de las columnas de 
                la tabla en la BD) del primer objeto de la coleccion, (ya que todos los objetos tienen los 
                mismos atributos) para recorrerlos con un foreach */ 
                ?>

                @if($campoNombre == 'remember_token' || $campoNombre == 'created_at' || $campoNombre == 'updated_at'
                 || $campoNombre == 'id' || $campoNombre == 'password' || $campoNombre == 'avatar' )
                <th style='display:none;'>{{$campoNombre}}</th>
                <?php //Cualquiera de estos campos, estaran ocultos a la vista del usuario, y solo se muestran
                // en el codigo ?>
                @else
                    <th>{{ ucfirst(str_replace('_', ' ', $campoNombre)) }}</th>
                    <?php 
                    /*A la hora de imprimir los nombres de las columnas, uso El metodo ucfirst, que
                    convierte la primera letra de una palabra en mayuscula, dentro de ucfirst llamo antes 
                    a la funcion replace, la cual cambia los _ de los nombres, por espacios reales entre 
                    palabras*/
                    ?>
                    
                @endif
                @endforeach
                <th>Opciones</th>
            </thead>
            <tbody>
        @foreach($usuarios as $usuario)
                <tr>
                    <td>{{$usuario->nick}}</td>
                    <td>{{$usuario->email}}</td>
                    <td>{{$usuario->fecha_alta}}</td>
                    <td>{{$usuario->tiempo_juego}} horas</td>
                    <td>{{$usuario->rol}}</td>
                    <td>
                        <button class='btn btn-info' value='{{$usuario->id}}' wire:click='mostrarFormEditar({{$usuario->id}},"usuario")'>Actualizar Usuario</button>
                        <button value='{{$usuario->id}}' wire:click='mostrarMenuBorrarUser("eliminarUser",{{$usuario->id}})' class='btn btn-danger'>Eliminar usuario</button>
                        <button value='{{$usuario->id}}' wire:click='mostrarFormEditar({{$usuario->id}},"inventario")' class='btn btn-info'>Modificar inventario</button>
                        <button value='{{$usuario->id}}' wire:click='reiniciarEstadisticas({{$usuario->id}})' class='btn btn-danger'>Reiniciar estadisticas</button>
                    </td>
                </tr>
        @endforeach
            </tbody>
        </table>
     
    </div>

    <div class='paginacion d-flex flex-row justify-content-center'>{{$usuarios->links('pagination::bootstrap-4')}}</div>
    @endif

    <?php /*con la funcion links le indico a laravel que genere los enlaces para poder paginar de 10 en 10
    usuarios, por los resultados de la consulta con todos los usuarios disponibles con rol usuario.
    
    Con estos enlaces puedo navegar entre resultados(pag 1 , 10 primeros resultados, pag 2 los 10 siguientes
    resultados pag 3 etc.)
    
    usando pagination::bootstrap como parametro, le indico a laravel, que aplique uno de los paquetes
    de bootstrap que lleva equipado por defecto,(en este caso estoy usando el paquete pagination
    exclusivo para estos casos) para poder maquetar y ajustar el tamaño de la paginacion a mi pagina,
    de manera bastante comoda
    
    Este paquete se encuentra en resources/views/vendor/pagination*/ ?>

</div>
</div>