<?php

namespace Database\Factories;

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
            'user_id' => User::inRandomOrder()->first()->id,
            'session_id' => Session::inRandomOrder()->first()->id,
            'total_price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
