<?php

namespace Database\Seeders;
//use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Mission;
use App\Models\MissionUser;
use App\Models\Item;
use App\Models\Enemigo;
use App\Models\savedGame;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


       $admin1 = User::factory()->create([//Puedo crear un usuario con los campos de su tabla y los valores que 
            //yo quiera para dichos campos
            'nick' => 'javitoba12',
            'email' => 'javitoba12@gmail.com',
            'password' => Hash::make('1234'),
            'fecha_alta' => now(),
            'tiempo_juego' => rand(1,100),
            'rol' => 'admin',
        ]);


        $usuarioPrueba=User::factory()->create([
            'nick' => 'juanjo22',
            'email' => 'juanjogarcia22@gmail.com',
            'password' => Hash::make('1234'),
            'fecha_alta' => now(),
            'tiempo_juego' => rand(1,100),
            
        ]);

        $itemPrueba=Item::factory()->create([

            'nombre' => 'Brebaje curativo (s)',
            
            'descripcion' => 'Este brebaje es una combinacion de varias hierbas y hongos medicinales
            encontrados en la naturaleza, mezclados con precision, molido y disuelto tras varios procesos
            alquimicos hasta desarrollar una solucion capaz de aumentar el proceso regenerativo de los 
            tejidos del cuerpo, gracias al efecto de los hongos tambien es capaz de mitigar parte del dolor 
            sufrido por el sujeto.',

            'efecto' => 'curativo',

            'magnitud' => 4,

            'duracion' => 3,

        ]);

       /* $enemigoPrueba=Enemigo::factory()->create([

            'nombre_enemigo' => 'Nosferatu',
            
            'descripcion' => 'Estas criaturas son demonios mayores y un escalon superior de la jerarquia
            vampirica, son capaces de reanimar los cadaveres que se encuentren a su alcance, tienen un
            aspecto particularmente desagradable, no muy diferente al de los cadaveres que reaniman, con
            la diferencia claro esta de que sus cuerpos no estan en descomposicion.',

            'debilidades' => 'Fuego, Sagrado',

            'daño' => 25,

            'tipo_daño' => 'Sangrante, Oscuro',
        ]);*/
        
        $misionPrueba=Mission::factory()->create([
            'nombre' => 'Atrapa al cultista',
            'tipo' => 'Captura',
            'objetivo' => 'Capturar al objetivo',
            'recompensa_puntos_base' => 1500,
        ]);
    
    
        

        
     $users = User::factory(23)->create();
     $users->push($usuarioPrueba);

     $misiones = Mission::factory(4)->create();
     $misiones->push($misionPrueba);

     $items = Item::factory(5)->create();
     $items->push($itemPrueba);

        //Enemigo::factory(4)->create();
        //savedGame::factory(6)->create();
        
    
        
     
        $faker = \Faker\Factory::create();

        foreach ($users as $user) {
            $cantidad=rand(1,5);
            $totalItems=Item::contarItems();
            $itemsConseguidos=$faker->numberBetween(1,$totalItems);
            // Asocio cada usuario con varios items
            $user->items()->attach(//Con attach puedo insertar nuevas filas en la tabla pivote item_user
                //(inventario), cuando uso attach justo despues de la funcion items quiere decir que las
                //filas que voy a insertar para la tabla item_user van a tener asociado la id del usuario
                //actual en su celda user_id, pues laravel asocia la id del usuario  
                // en el momento en el que desde user se llama a la funcion items, (funcion
                // que tiene declarada la relacion N-N entre usuario e item) automaticamente.

                $items->random($itemsConseguidos)->pluck('id')->toArray(), // De la tabla items recojo x items cualesquiera
                //cuando uso la funcion random sobre items (aludiendo a la tabla items) en laravel, 
                //, de esos items, usando la funcion pluck me llevo solamente el valor de la celda id, 
                // esos id se devuelven como una coleccion, por ultimo, 
                // convierto la coleccion en array porque attach trabaja con arrays en lugar de colecciones

                //La funcion random es similar a inRandomOrder, con la diferencia de que random solo se puede
                //utilizar sobre colecciones, y la funcion inRandomOrder trabaja directamente sobre querys
                //antes de extraer las filas

                //Como he creado los items mas arriba y los tengo almacenados ya como coleccion en una variable,
                //la mejor opcion es usar la funcion random que trabaja con colecciones, no podria utilizar 
                //inRandomOrder para colecciones, ni tampoco podria haberlo usado en una consulta, porque 
                //aun no estaban rellenadas las filas de la tabla items

                ['cantidad' => $cantidad] //a las filas que voy a insertar en la tabla usuario
                //les asigno a todas un random, todas las filas de mi array items usaran el mismo numero 
                // random generado para sus campos cantidad.
            );
        }

       // MissionUser::factory(15)->create();

       //En el caso de MissionUser me he visto obligado a usar un seeder con un bucle para asi
       //poder controlar el numero de misiones completadas por el usuario, pero intentar usar 
       //un factory para este fin me produce demasiados errores por mas que intente pulir el codigo
       
       $this->call(MissionUserSeeder::class);

       $this->call(EnemigoUserSeeder::class);
    }
}
