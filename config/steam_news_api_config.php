<?php

/*return [

    'appid' => 582010, //id del juego
    'count' => 5, //numero de noticias que quiero extraer
    'maxlength' => 300 // numero de caracteres por noticias, dentro de su contenido

    //appid de stalker 2 --> 1643320

];*/

return [

    'appid' => env('STEAM_APP_ID', 582010), //id del juego, con env miro en el archivo env, o en las variables de entorno de railway(si estoy en produccion)
    //si existe la variable STEAM_APP_ID, y en caso de no existir, uso el valor por defecto para esa variable (582010, la id de monster hunter en steam),
    // si existe un valor establecido en las variables de entorno de railway(produccion), o en el archivo env en local, uso ese valor en lugar del valor por defecto, lo 
    //mismo aplica al resto de variables que devuelvo en este .config

    'count' => env('STEAM_COUNT', 5), //numero de noticias que quiero extraer
    'maxlength' => env('STEAM_MAX_LENGTH', 300) // numero de caracteres por noticias, dentro de su contenido

    //appid de stalker 2 --> 1643320

];

 ?>

