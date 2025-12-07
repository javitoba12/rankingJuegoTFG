

<!DOCTYPE html>
<html lang="en">
<?php

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            background-image: url('{{asset('images/dracula_castle.jpg')}}');
            background-size:cover;
            background-repeat:no-repeat;
          /*  background-position:center;*/
        }
        
        nav{
            background-color:black;
        }
    </style>
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<body>
    <!--<nav><h1>Bienvenido</h1></nav>-->
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
     integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
     crossorigin="anonymous"></script><!--Recomendacion de bootstrap, poner este script al final del
     body-->
</body>
</html>