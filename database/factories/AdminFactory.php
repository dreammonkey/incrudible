<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Incrudible\Models\Admin>
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
