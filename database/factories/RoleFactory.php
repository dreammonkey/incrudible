<?php

namespace Database\Factories;

use App\Incrudible\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Incrudible\Incrudible\Facades\Incrudible;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array{name: string, guard_name: string}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'guard_name' => Incrudible::guardName(),
        ];
    }
}
