<!--<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
</div>-->


<div class="container  bg-dark formulario-registro-container">
            <h1 class="mx-auto">Registro</h1>
            <form wire:submit="registro" class="was-validated">
                <div class="mb-3 mt-3">
                <label for="nickname" class="form-label">Usuario:</label>
                <input type="text" class="form-control" id="nickname" placeholder="Introduzca su Nickname" name="nickname" wire:model="nick" required>
                <div class="valid-feedback">Correcto.</div>
                <div class="invalid-feedback">Por favor rellene este campo.</div>
                </div>

                <div class="mb-3 mt-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" placeholder="Introduzca su email" wire:model="email" name="email" required>
                <div class="valid-feedback">Correcto.</div>
                <div class="invalid-feedback">Por favor rellene este campo.</div>
                
                @if($privilegios)
                    <label for="roles">Tipo de usuario:</label><br>
                    <select name="" id="roles" wire:model='rol'>
                        <option value="usuario">Usuario</option>
                        <option value="admin">Administrador</option>
                    </select>
                @endif
                </div>

                <div class="mb-3">
                <label for="password" class="form-label">Clave:</label>
                <input type="password" class="form-control" id="password" placeholder="Introduzca su contraseña" wire:model="password" name="password" required>
                <div class="valid-feedback">Correcto.</div>
                <div class="invalid-feedback">Por favor rellene este campo.</div>
                <label for="password2" class="form-label">Confirmar clave:</label>
                <input type="password" class="form-control" id="password2" placeholder="Introduzca de nuevo su contraseña" wire:model="password2" name="password2" required>
                <div class="valid-feedback">Correcto.</div>
                <div class="invalid-feedback">Por favor rellene este campo.</div>
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
                        <?php /*Pinto cada error del array en un alert diferente*/?>
                        </div>
                    @endforeach
                @endif


                @if(session()->has('error'))
                  <div class="alert alert-danger" id="loginError" style="display:block ;">
                      <strong>Error:</strong> {{session('error')}}
                  </div>
                @endif

                <div class="row mb-3">
                    <div class="col text-start">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
                <div class="col text-end">
                    <button type="reset" class="btn btn-danger">Cancelar</button>
                    <button type="reset" wire:click='volver' class="btn btn-info">Volver</button>
                </div>
                
                </div>
            
        </form>
      </div>


