
<div>
 <nav class="navbar navbar-expand-sm bg-light navbar-light bg-dark navbar-dark ">
  <div class="container-fluid justify-content-end">
    <ul class="navbar-nav">

    <li class="nav-item mx-5">
        <a class="nav-link" href="#"><h6>Monster Hunter</h6></a>
      </li>

      <li class="nav-item">
        <a class="nav-link active" href="/inicio/login">Login</a>
      </li>
     <li class="nav-item">
        <a class="nav-link" href="/inicio/registro">Registro</a>
      </li>
      
    <!--  <li class="nav-item">
        <a class="nav-link disabled" href="#">Disabled</a>
      </li> -->
    </ul>
  </div>
</nav>

@if(isset($mensaje))
    <div class="alert alert-success" id="loginSuccess" >
      <strong>{{$mensaje}}</strong>
    </div>
@endif

<div class="overflow-auto p-3 mb-2 bg-dark bg-gradient text-white mt-5 w-50 p-3 border border-warning  rounded-top">

<h2>Noticias Recientes ! :</h2>

@if(isset($noticias) && count($noticias) > 0)

  

    

    @foreach($noticias as $noticia)

    <div class='bg-dark rounded px-2'>
      <h4>{{ $noticia['titulo'] }}</h4>
      <p>{{ $noticia['fecha'] }}</p>
      <p>{{ $noticia['descripcion'] }} <a href="{{ $noticia['url'] }}" target="_blank">ver más</a></p>
      
      
    </div>

    @endforeach
@else
 <h4>No se pudieron cargar las noticias.</h4>
@endif

 </div>
</div>