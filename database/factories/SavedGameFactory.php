<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\savedGame;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\savedGame>
 */
class SavedGameFactory extends Factory
{

    use HasFactory, Notifiable;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>rand(1,4),
            'nombre_mision' => $this->faker->word(),
            'nombre_partida' => $this->faker->word(),
            'estado_personaje' => $this->faker->sentence(),
            'fecha_guardado' => now(),
           // 'remember_token' => Str::random(10)
        ];
    }
}
