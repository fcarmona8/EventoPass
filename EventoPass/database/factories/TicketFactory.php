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
        $purchaseIds = Purchase::pluck('id')->toArray();
        $typeIds = TicketType::pluck('id')->toArray();
        $sessionIds = Session::pluck('id')->toArray();

        return [
            'purchase_id' => $purchaseIds[array_rand($purchaseIds)],
            'type_id' => $typeIds[array_rand($typeIds)],
            'session_id' => $sessionIds[array_rand($sessionIds)],
        ];
    }
}
