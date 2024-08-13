<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Incrudible\Models\Model>
 */
class AdminFactory extends Factory
{
    protected $model = \App\Incrudible\Models\Admin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->userName(),
            'email' => $this->faker->email(),
            'password' => Hash::make($this->faker->password()),
            'remember_token' => Str::random(10),
        ];
    }
}
