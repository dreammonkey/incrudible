<?php

namespace Database\Factories;

use App\Incrudible\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Incrudible\Incrudible\Facades\Incrudible;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array{name: string, guard_name: string}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'guard_name' => Incrudible::guardName(),
        ];
    }
}
