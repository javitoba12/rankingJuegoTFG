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
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        @livewireStyles
        <title>Monster Hunter</title>
        <!--{{ $title ?? 'Page Title' }}-->
        <style>
            html,body{
                width: 100%;
                /*height: 100%;*/
                min-height: 100vh;

                margin:0;
            }

            body{
            background-image: url('{{asset('images/mh_world3.jpg')}}');
            background-size:cover;
            background-repeat:repeat;
          /*  background-position:center;*/
        }

        
        .formulario-container{
            display:flex;
            flex-direction:column;
            flex-wrap:nowrap;
            width: 70%;
            height: auto;
            
            /*height:auto;*/
            border-radius:9px;
            /* margin-top:20px*/

            }

            .formulario-container>h1{
            width: auto;
        }
        .formulario-container>form{
            width: 100%;
            /*height:100%;*/
            min-height:auto;
        }

        .formulario-registro-container{
            width: 80%;
            /*height:100%;*/
            min-height: 100vh;

        }


        .container-claro{
            background-color:gainsboro;
            border:2px solid black;
        }

        

        .main-container{
            
            width: 100%;
            /*height:100%;*/
            min-height: 100vh;
            margin-bottom:0px;
        }

       /* .nav2-container{
            display:inline;
            width: 30%;
        }*/

        .nav-container{
            width: 100%;
        }

        .my-nav{
            width: 100%;
        }

        .options-container{/**/
            display:inline;
            width: 20%;
        }

        .options-container>button{
            margin-left:5px;
        }

        .ranking-container{/*De la vista de ranking, el div principal debajo de nav*/
            
            border-top-left-radius:9px;
            border-top-right-radius:9px;
            width: 100%;
            height:80vh;
            /*padding-left:0px;
            padding-right:0px;*/
            overflow:auto;

        }


        .table-ranking-container{
            width: 50%;
            height:auto;
        }


        .table-ranking-container>table{
            width: 90%;
            height:auto;
            
        }

        .select-ranking-container{

            width: 90%;

        }

        /*PAGINA DE PERFIL*/

        .perfil-container{/*De la vista de perfil*/
            width: 36%;
            height:70vh;
            border-bottom-right-radius:9px;
            overflow: auto;
            /*height:650px;*/
        }

        .perfil-container>button{
            margin-bottom:10px;
            margin-left:5px;
        }

        .user-informacion-container{
            width: 100%;
        }

        .user-datos{
            width: 65%;
        }

        .user-avatar{
            
            width: 30%;
        }

        .user-avatar>img{
            width: 100%;
            max-width:200px;
            max-height:200px;
        }

        /*PAGINA DE INVENTARIO*/

        .inventarioContainer{
            background-image: url('{{asset('images/inventory.jpg')}}');
            background-size:cover;
            background-repeat:no-repeat;
            overflow:auto;
            width: 35%;
            height:90vh;
            border-top-right-radius:9px;
            border-bottom-right-radius:9px;
            padding-left:8px;
            margin-top:10px;
            margin-bottom:10px;
            color:gainsboro;
            font-size:bold;
        }

        .inventarioContainer>ul{
            list-style: none;
        }

        .opcionesBtn{
            margin-left:10px;
            margin-bottom:10px;
        }

        /*PAGINA BAJAS*/

        /*.bajas-page{
            background-image: url('{{asset('images/hunting.jpg')}}');
            height: 100% ;
            margin:0;
            
        }*/

        .enemigosContainer{
             background-image: url('{{asset('images/bajas.jpg')}}');
            background-size:cover;
            background-repeat:no-repeat;
            overflow:auto;
            width: 35%;
            height:90vh;
            border-top-right-radius:9px;
            border-bottom-right-radius:9px;
            padding-left:8px;
            margin-top:10px;
            margin-bottom:10px;
            color:gainsboro;
            font-size:bold;
        }

        .enemigosContainer>ul{
            list-style: none;
        }

        /*PAGINA DETALLE*/

        .mainDetalle{
            width: 45%;
            height:49vh;
            border-bottom-right-radius:9px;
        }

        .containerDetalle{
            width: 98%;
            height:40vh;
            background-image: url('{{asset('images/inventory.jpg')}}');
            background-size:cover;
            background-repeat:no-repeat;
            overflow:auto;
            /*border-top-right-radius:9px;*/
            border-bottom-right-radius:9px;
            margin-bottom:10px;
        }

        /*PAGINA ADMINISTRACION*/

        .adminTitulo{
            color:green;
        }

        .adminInformacionContainer{

            border-top-left-radius:9px;
            border-top-right-radius:9px;
            width: 95%;
            height:auto;
            
            
        }

        .adminInformacionContainer{

            /*height:700px;
            overflow:auto;*/

        }

        


        .paginacion{
            width: 30%;
           
            
        }

        .editUser{
            display:flex;
            flex-direction:row;
            flex-wrap:wrap;
            justify-content:space-around;
            width: 90%;
            height:auto;
            padding-bottom:10px;
            border-radius:9px;
        }


        .editInventary{
            display:flex;
            flex-direction:row;
            flex-wrap:wrap;
            justify-content:space-around;
            width: 96%;
            height:auto;
            padding-bottom:10px;
            border-radius:9px;
        }



        .editUser>div>input{
            margin-top:5px;
            
        }

        

        .editUser button{

            margin-top:5px;
        }

        .btonForm{
            display:flex;
            justify-content:space-between;
            width: 96%;
            
        }

        .btonForm>button{

            width: 80px;

        }

        .editInventary> .btonForm{
            
            margin-top:9px;
        }

        .editInventary> .btonForm > button {
            width: 28vh;
        }



        </style>
    </head>
    <body class="bg-dark text-light bt-5">
        {{ $slot }}

        

     <!--scripts liveware-->
        @livewireScripts
        @livewireChartsScripts
        @fluxScripts

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
     integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
     crossorigin="anonymous"></script><!--Recomendacion de bootstrap, poner este script al final del body-->
    </body>
</html>
