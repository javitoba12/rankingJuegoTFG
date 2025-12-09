<?php

    namespace App\Services;

   class utilidades {
     private static $colorFondo;
     private static $colorTexto;

        public static function aplicarColor($usuario){
            if($usuario->tema == 'oscuro'){
                self::$colorFondo='bg-dark';
                self::$colorTexto='text-white';
            }else{

                self::$colorFondo='bg-light';
                self::$colorTexto='text-dark';

            }
        }

        public static function getBgColor(){
            return self::$colorFondo;
        }

        public static function getTextColor(){
            return self::$colorTexto;
        }

   }
?>