<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Incrudible\Incrudible\Facades\Incrudible;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Incrudible\Models\Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = \App\Incrudible\Models\Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'guard_name' => Incrudible::guardName(),
        ];
    }
}
