<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;
use App\Models\TicketType;
use App\Models\Session;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Role;
use App\Models\Ticket;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Crea algunos roles
        $promoterRole = Role::factory()->promoter()->create();
        $adminRole = Role::factory()->administrator()->create();

        // Crea algunos usuarios específicos
        User::factory()->promoter()->create(['role_id' => $promoterRole->id]);
        User::factory()->promoterTwo()->create(['role_id' => $promoterRole->id]);
        User::factory()->promoterThree()->create(['role_id' => $promoterRole->id]);
        User::factory()->promoterFour()->create(['role_id' => $promoterRole->id]);

        // Crear categorías específicas usando la factory
        $concerts = Category::factory()->concerts()->create();
        $festivals = Category::factory()->festivals()->create();
        $conferences = Category::factory()->conferences()->create();
        $theatre = Category::factory()->theatre()->create();
        $sports = Category::factory()->sports()->create();
        $arts = Category::factory()->arts()->create();
        $movies = Category::factory()->movies()->create();
        $music = Category::factory()->music()->create();
        $dance = Category::factory()->dance()->create();
        $literature = Category::factory()->literature()->create();

        // Crear venues y otros datos
        Venue::factory()->count(10)->create();
        TicketType::factory()->count(5)->create();

        // Crear eventos asignados a categorías específicas
        $categories = Category::all();
        foreach ($categories as $category) {
            Event::factory()->count(6)->create(['category_id' => $category->id]);
        }

         // Crear un arreglo global para rastrear los tickets disponibles para cada tipo
        $globalTicketTypes = TicketType::all()->pluck('available_tickets', 'id')->toArray();

        // Crear eventos, sesiones, compras y tickets
        Event::all()->each(function ($event) use (&$globalTicketTypes) {
            Session::factory()->count(rand(1, 3))->create(['event_id' => $event->id])
                ->each(function ($session) use (&$globalTicketTypes) {
                    Purchase::factory()->count(rand(1, 5))->create(['session_id' => $session->id])
                        ->each(function ($purchase) use (&$globalTicketTypes, $session) {
                            foreach ($globalTicketTypes as $typeId => &$availableTickets) {
                                $ticketsToCreate = min(rand(1, 4), $availableTickets);

                                Ticket::factory()->count($ticketsToCreate)->create([
                                    'purchase_id' => $purchase->id,
                                    'type_id' => $typeId,
                                    'session_id' => $session->id
                                ]);

                                $availableTickets -= $ticketsToCreate;
                            }
                        });
                });
        });
    }
}