<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should be a promoter.
     */
    public function promoter(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'promotor1',
                'email' => 'promotor1@test.com',
                'password' => Hash::make('p12345678'),
                'role_id' => 1,
            ];
        });
    }

    /**
     * Indicate that the user should be a second promoter.
     */
    public function promoterTwo(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'promotor2',
                'email' => 'promotor2@test.com',
                'password' => Hash::make('p2345678'),
                'role_id' => 1,
            ];
        });
    }

    public function promoterThree(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'promotor3',
                'email' => 'aperez@alumnat.copernic.cat',
                'password' => Hash::make('12345678'),
                'role_id' => 1,
            ];
        });
    }

    public function promoterFour(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'promotor4',
                'email' => 'gpuigantell@alumnat.copernic.cat',
                'password' => Hash::make('123456789'),
                'role_id' => 1,
            ];
        });
    }
}
