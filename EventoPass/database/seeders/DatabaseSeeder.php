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

        // Crear categorías
        Category::factory()->concerts()->create();
        Category::factory()->festivals()->create();
        Category::factory()->conferences()->create();
        Category::factory()->theatre()->create();
        Category::factory()->sports()->create();
        Category::factory()->arts()->create();
        Category::factory()->movies()->create();
        Category::factory()->music()->create();
        Category::factory()->dance()->create();
        Category::factory()->literature()->create();

        // Crear recintos (venues)
        Venue::factory(10)->create(); // Crear 10 recintos

        // Crear eventos
        Event::factory(30)->create(); // Crear 30 eventos

        // Crear sesiones
        Session::factory(50)->create(); // Crear 50 sesiones

        // Crear tipos de ticket
        TicketType::factory(10)->create(); // Crear 10 tipos de ticket

        // Crear compras
        Purchase::factory(100)->create(); // Crear 100 compras

        // Crear tickets
        Ticket::factory(200)->create(); // Crear 200 tickets
    }
}