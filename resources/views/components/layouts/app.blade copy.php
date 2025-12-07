<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!--bootstrap-->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

     <!--livewire-->
        @fluxAppearance
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
        @livewireStyles
        <title>{{ $title ?? 'Page Title' }}</title>
        <style>
            html,body{
                width: 100%;
                height: 100%;
            }

            body{
            background-image: url('{{asset('images/dracula_castle.jpg')}}');
            background-size:cover;
            background-repeat:no-repeat;
          /*  background-position:center;*/
        }

        
        .formulario-container{
            display:flex;
            flex-direction:column;
            flex-wrap:nowrap;
            width: 70%;
            height:auto;
            border-radius:9px;
            /* margin-top:20px*/

            }

            .formulario-container>h1{
            width: auto;
        }
        .formulario-container>form{
            width: 100%;
            height:100%;
        }

        .formulario-registro-container{
            width: 80%;
            height:100%;
  

        }

        .main-container{
            
            width: 100%;
            height:100%;
        }

       /* .nav2-container{
            display:inline;
            width: 30%;
        }*/

        .options-container{/**/
            display:inline;
            width: 30%;
        }

        .ranking-container{/*De la vista de ranking, el div principal debajo de nav*/
            
            border-top-left-radius:9px;
            border-top-right-radius:9px;
            width: 80%;
            height:600px;
        }

        /*PAGINA DE PERFIL*/

        .perfil-container{/*De la vista de perfil*/
            width: 30%;
            height:650px;
        }

        .perfil-container>button{
            margin-bottom:10px;
            margin-left:5px;
        }

        /*PAGINA DE INVENTARIO*/

        .inventarioContainer>ul{
            list-style: none;
        }

        .opcionesBtn{
            margin-left:10px;
            margin-bottom:10px;
        }

        .adminTitulo{
            color:green;
        }


        </style>
    </head>
    <body class="bg-dark text-light bt-5">
        {{ $slot }}

        

     <!--scripts liveware-->
        @livewireScripts
        @fluxScripts

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
     integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
     crossorigin="anonymous"></script><!--Recomendacion de bootstrap, poner este script al final del body-->
    </body>
</html>
