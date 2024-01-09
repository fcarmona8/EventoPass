<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    /**
     * Indicate that the role is a promoter.
     */
    public function promoter(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'promotor',
            ];
        });
    }

    /**
     * Indicate that the role is an administrator.
     */
    public function administrator(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'administrador',
            ];
        });
    }
}
