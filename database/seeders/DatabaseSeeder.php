<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;
use App\Models\TicketType;
use App\Models\Ticket;
use App\Models\Session;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Role; // Asegúrate de importar el modelo de Role

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Crea algunos roles
        $promoterRole = Role::factory()->promoter()->create(); // Supongamos que esto crea el rol con ID 1
        $adminRole = Role::factory()->administrator()->create(); // Supongamos que esto crea el rol con ID 2

        // Crea algunos usuarios específicos
        User::factory()->promoter()->create(['role_id' => $promoterRole->id]);
        User::factory()->promoterTwo()->create(['role_id' => $promoterRole->id]);
        User::factory()->promoterThree()->create(['role_id' => $promoterRole->id]);
        User::factory()->promoterFour()->create(['role_id' => $promoterRole->id]);

        // El resto de tus factories...
        Category::factory()->count(10)->create();
        Venue::factory()->count(10)->create();
        TicketType::factory()->count(5)->create();
        Event::factory()->count(30)->create()->each(function ($event) {
            Session::factory()->count(rand(1, 3))->create(['event_id' => $event->id])
                ->each(function ($session) {
                    Purchase::factory()->count(rand(1, 5))->create(['session_id' => $session->id])
                        ->each(function ($purchase) {
                            Ticket::factory()->count(rand(1, 4))->create([
                                'purchase_id' => $purchase->id,
                                'type_id' => TicketType::inRandomOrder()->first()->id
                            ]);
                        });
                });
        });
    }
}
