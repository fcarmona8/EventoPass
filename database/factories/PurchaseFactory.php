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
        $userIds = User::pluck('id')->toArray();
        $sessionIds = Session::pluck('id')->toArray();

        return [
            'user_id' => $userIds[array_rand($userIds)],
            'session_id' => $sessionIds[array_rand($sessionIds)],
            'total_price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
