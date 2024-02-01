<?php

namespace Database\Factories;

use App\Models\Session;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition()
    {
        $sessionIds = Session::pluck('id')->toArray();

        return [
            'session_id' => $this->faker->randomElement($sessionIds),
            'name' => $this->faker->name,
            'dni' => $this->faker->numerify('#########'),
            'phone' => $this->faker->numerify('#########'), 
            'email' => $this->faker->unique()->safeEmail,
            'total_price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
