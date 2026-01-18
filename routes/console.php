<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;//Libreria importada para programar tareas o comandos

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('app:actualizar-monstruos')->everyMinute();//llamada al comando para actualizar los monstruos en la BD cuando se ejecuten los comandos de schedule



//->everyMinute()
//daily()