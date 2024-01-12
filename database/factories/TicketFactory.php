<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Purchase;
use App\Models\Session;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'purchase_id' => Purchase::inRandomOrder()->first()->id,
            'type_id' => TicketType::inRandomOrder()->first()->id,
            'session_id' => Session::inRandomOrder()->first()->id,
        ];
    }
}
