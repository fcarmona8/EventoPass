<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Purchase;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'purchase_id' => Purchase::factory(),
            'type_id' => TicketType::factory(),
        ];
    }
}
