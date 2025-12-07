<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Enemigo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enemigo>
 */
class EnemigoFactory extends Factory
{

    use HasFactory,Notifiable;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
           // 'user_id' => rand(1,4),
            'nombre_enemigo' => $this->faker->name(),
            'descripcion' => $this->faker->sentence(),
            'debilidades' => $this->faker->sentence(),
            'daño' => $this->faker->numberBetween(3,20),
            'tipo_daño' =>$this->faker->word(),
            'remember_token' => Str::random(10)
        ];
    }
}
