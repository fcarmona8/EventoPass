<?php

namespace Database\Factories;

use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTypeFactory extends Factory
{
    protected $model = TicketType::class;

    public function definition()
    {
        static $typeCounter = 0;

        $typeNames = ["General", "VIP", "Platino", "Deluxe", "Económico", "Estudiantil", "Premium", "Gold", "Silver", "Bronze"];

        $typeData = [
            'name' => $typeNames[$typeCounter % count($typeNames)],
            'price' => $this->faker->randomFloat(2, 10, 200),
            'available_tickets' => $this->faker->numberBetween(1, 5) 
        ];

        $typeCounter++;
        return $typeData;
    }
}
