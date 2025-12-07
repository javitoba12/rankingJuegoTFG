

        <div class="container mt-4 formulario-container bg-dark">
            <h1 class="mx-auto">Login</h1>
            <form class="was-validated" wire:submit="login" >
           
                <div class="mb-2">
                    <label for="email" class="form-label">Usuario:</label>
                    <input type="email" class="form-control" id="email"  wire:model="email" placeholder="Introduzca su email" name="email" required>
                    <div class="valid-feedback">Correcto.</div>
                    <div class="invalid-feedback">Por favor rellene este campo.</div>
                </div>
                <div class="mb-2">
                    <label for="password" class="form-label">Clave:</label>
                    <input type="password" class="form-control"  wire:model="password" id="password" placeholder="Introduzca su contraseña" name="password" required>
                    <div class="valid-feedback">Correcto.</div>
                    <div class="invalid-feedback">Por favor rellene este campo.</div>
                </div>

                <div class="form-check mb-3">
                    <label class="form-label">
                        <input class="form-input" type="checkbox" name="remember"> Recuerdame
                    </label>
                </div>
                <div class="mb-3 col text-end">
                    <a href="#">¿Olvidaste tu contraseña?</a>
                    <a href="/inicio/registro">Registrate</a>
                </div>

                <div class="alert alert-success" id="loginSuccess" style="display:none ;">
                    <strong>Bienvenido</strong> a la web.
                </div>

                @if($errors->any())<!--Si el array $errors (array que laravel 11 proporciona de manera 
                automatica y comoda) contiene algun error en el momento de ejecutar el formulario de
                login...-->

                <!--Hago un bucle foreach y creo tantos div alert de botstrap como errores 
                contenga $errors-->
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger" id="loginError" style="display:block ;">
                            <strong>Error:</strong> {{ $error }}
                        <!--Pinto cada error del array en un alert diferente-->
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
                        <button type="submit" class="btn btn-primary" >Entrar</button>
                    </div>
                    <div class="col text-end">
                        <button type="reset" class="btn btn-danger">Cancelar</button>
                        <button type="reset" wire:click='volver' class="btn btn-info">Volver</button>
                    </div>
                </div>
            </form>
        </div>

 
