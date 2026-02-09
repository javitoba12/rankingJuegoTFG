<div class="container ms-0 perfil-container {{ $tema['bgColor'] }}  {{ $tema['textColor'] }} ">
    <!--{{-- In work, do what you enjoy. --}}-->

    @if(isset($aviso))
        <p><b>{{ $aviso }}</b></p>
        
    @endif

    <div class='user-informacion-container d-flex flex-row mb-2'>
        <div class='user-datos mt-2'>
                @if(!empty($usuario))
            @if($usuario->rol == 'admin')
                <h4>Posee privilegios de administrador</h4>
            @endif
            
            <h1>Usuario: {{$usuario->nick}}</h1>
            <p>Email: {{$usuario->email}}</p>
            
            <p>Horas jugadas: {{$usuario->tiempo_juego}} horas</p>
            <p>Registrado en: {{$usuario->fecha_alta}}</p>

            @if(!session()->has('perfilSeleccionado'))

                <select name="" id="" class="form-select form-select-sm selectColor text-light bg-secondary" wire:model="colorSeleccionado">
                    <option value="" selected disabled>Cambiar de color</option>
                    <option value="claro">Claro</option>
                    <option value="oscuro">Oscuro</option>
                </select>

                <?php //AVATAR DEL USUARIO ?>

                <!--acept= image/* para que el input file solo acepte archivos con formato de imagen(jpg,png,gif,jpgeg...)-->
                
                
                <input type="file" wire:model="avatar" class="btn" accept="image/*"> {{-- Para cambiar el avatar del usuario 
                wire model enlaza este input file, con la propiedad avatar del componente livewire de perfil ,
                livewire se encarga de que cuando la propiedad publica del componente cambie, se llame automaticamente a la funcion updatedAvatar --}}
                
            

            @endif
            
            

        </div>

        <div class='user-avatar mx-1 mt-2'> <?php //IMAGEN DEL AVATAR ?>
        

            <img src="{{ $avatarUrl }}" class='rounded-circle border border-primary avatar' alt="avatar">
        
        </div>
    </div>

    @if(!session()->has('perfilSeleccionado')) <?php //En caso de que el usuario actual acceda a su propio perfil, y no al perfil de otro usuario seleccionado ?>
        <button type='button' class='btn btn-success' wire:click="$set('actualizar', true)">Editar Perfil</button>
        <button type='button' class='btn btn-success' wire:click="actualizarPuntuacion">Actualizar mi puntuacion</button><br>
       <!-- <button type='submit' class='btn btn-info' wire:click="mostrarPartidas">Mis partidas</button>-->
        
        {{-- usando $set en wire:click puedo cambiar directamente el valor de una variable,
        en set('nombreVariable','nuevoValorasignado') --}}

        @if($usuario->rol != 'admin')
            <button type='button' class='btn btn-danger' wire:click="$set('borrar', true)">Eliminar mi usuario</button>
        @endif

         @if($usuario->rol == 'admin'){{-- //Si el usuario es admin, doy acceso a la opcion de navegar
         //a administracion --}}

            <button type='button' class='btn btn-info' wire:click='rutaAdmin'>Navegar a Administracion</button>
        @endif
    @endif
        <button type='button' class='btn btn-info' wire:click='volver'>Volver</button><br><br>
    
        @if($actualizar){{-- //Si el valor actualizar esta en true, creo y muestro el siguiente 
        // formulario para actualizar los datos del usuario --}}
        
            <form wire:submit.prevent='editarPerfil'>
                <label for="nick">Nick:</label>
                <input type="text" name="nick" id="nick" wire:model='nuevoNick' value='{{$usuario->nick}}'><br>
                <label for="clave">Nueva clave:</label>
                <input type="password" name="clave" id="clave" wire:model='nuevaPassword' value=""><br><br>
                <button class='btn btn-success' type='submit'>Confirmar</button>
                <button class='btn btn-info' wire:click='refrescar'>Cancelar</button>
            </form>

        @endif

        @if($borrar){{-- //Al igual que con actualizar, muestro el formulario si borrar es true --}}
            <form wire:submit='borrarUsuario'>
                <h3>¿Esta seguro de querer borrar su usuario?</h3>
                <label for="clave">Introduzca su contraseña para autorizar la operacion:</label>
                <input type="password" name="clave" id="clave" wire:model='nuevaPassword' value="">
                <button class='btn btn-warning' type='submit'>Confirmar</button>
                
                @if (session()->has('error'))
                    <p><b>{{session('error')}}</b></p>
                @endif
            </form><br>
            <button class='btn btn-success' wire:click='refrescar'>Cancelar</button>
        @endif
    @endif

    <script>
     window.addEventListener('recargarPagina', function () {//Creo un evento asociado a la pagina actual

        window.location.reload();//Cuando este evento se dispare, provocara que se recargue la pagina actual

     });
  </script>  
  @if($errors->any())<!--Si el array $errors (array que laravel 11 proporciona de manera 
                automatica y comoda) contiene algun error en el momento de ejecutar el formulario de
                login...-->

                <!--Hago un bucle foreach y creo tantos div alert de botstrap como errores 
                contenga $errors-->
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger" id="perfilError" style="display:block ;">
                            <strong>Aviso:</strong> {{ $error }}
                        <!--Pinto cada error del array en un alert diferente-->
                        </div>
                    @endforeach
    @endif
</div>
