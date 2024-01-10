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
use App\Models\Role;

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

        // Crea categorías específicas y recupera sus ID
        $concertsId = Category::factory()->concerts()->create()->id;
        $festivalsId = Category::factory()->festivals()->create()->id;
        $conferencesId = Category::factory()->conferences()->create()->id;
        $theatreId = Category::factory()->theatre()->create()->id;
        $sportsId = Category::factory()->sports()->create()->id;

        // Crear venues y otros datos
        Venue::factory()->count(10)->create();
        TicketType::factory()->count(5)->create();

        // Creación de eventos asignados a categorías específicas
        Event::factory()->count(6)->create(['category_id' => $concertsId]);
        Event::factory()->count(6)->create(['category_id' => $festivalsId]);
        Event::factory()->count(6)->create(['category_id' => $conferencesId]);
        Event::factory()->count(6)->create(['category_id' => $theatreId]);
        Event::factory()->count(6)->create(['category_id' => $sportsId]);

        // Creación de sesiones, compras y tickets
        Event::all()->each(function ($event) {
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
