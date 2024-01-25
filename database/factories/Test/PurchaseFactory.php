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
            'user_id' => User::factory(),
            'session_id' => Session::factory(),
            'total_price' => $this->faker->randomFloat(2, 100, 500),
        ];
    }
}
