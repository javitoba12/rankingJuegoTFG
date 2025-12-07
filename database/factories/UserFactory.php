<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    use HasFactory,Notifiable;
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;//Esa interrogacion indica en php que la variable es un string,
    //pero puede almacenar (o mas bien se espera que pudiese...) un null, asi que de esta manera evito errores.
    //Asi si recivo un null en password, el programa no falla necesariamente y puedo contemplar la posibilidad de recibir
    //un null, y actuar en base a ello como pasa de hecho mas abajo.


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //Para cuando quiero crear varios usuarios automaticamente
        return [
            'nick' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),//Esto guarda la contraseña ya hasheada, si la contraseña 
            // aun no esta hasheada(quiere decir que password es null, este operador ??= esta preguntando si la 
            // password es null), la hasheo usando hash make una sola vez, como password es un atributo estatico, 
            // esto quiere decir que todos los usuarios creados en factory comparten la misma contraseña 
            // con el mismo hash exacto, para evitar usar hash:make tantas veces como usuarios se hayan creado y asi 
            // evitar gastar tantos recursos(dado que con ??= tambien estoy evitando que cada usuario lleve misma clave pero
            // distinto hash). Esto es una buena practica para crear datos de prueba.
            'fecha_alta' => $this->faker->date(),
            'tiempo_juego' => $this->faker->numberBetween(1,200),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {//Este metodo lo proporciona automaticamente Laravel, permite registrar en la web a usuarios con un email que no han verificado.
    
    //Ideal para crear usuarios de prueba.
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
