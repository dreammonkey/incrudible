<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Incrudible\Incrudible\Facades\Incrudible;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = \App\Incrudible\Models\Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'guard_name' => Incrudible::guardName(),
        ];
    }
}
