<?php

namespace Database\Factories\Test;

use App\Models\User;
use App\Models\Session;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'dni' => $this->faker->randomNumber,
            'phone' => $this->faker->randomNumber,
            'session_id' => Session::factory(),
            'total_price' => $this->faker->randomFloat(2, 100, 500),
        ];
    }
}
