
<div>
 <nav class="navbar navbar-expand-sm bg-light navbar-light bg-dark navbar-dark ">
  <div class="container-fluid justify-content-end">
    <ul class="navbar-nav">

    <li class="nav-item mx-5">
        <a class="nav-link" href="#"><h6>Project Dracokeos</h6></a>
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
</div>