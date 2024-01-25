<?php

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;
use Database\Factories\Test\CategoryFactory;
use Database\Factories\Test\VenueFactory;
use Database\Factories\Test\EventFactory;
use Database\Factories\Test\SessionFactory;
use Database\Factories\Test\TicketTypeFactory;
use Database\Factories\Test\PurchaseFactory;
use Database\Factories\Test\TicketFactory;
use Database\Factories\Test\EventImageFactory;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        CategoryFactory::new()->count(10)->create();

        VenueFactory::new()->count(10)->create();

        EventFactory::new()->count(10)->create()->each(function ($event) {
            SessionFactory::new()->create([
                'event_id' => $event->id,
                'date_time' => $event->event_date
            ]);

            EventImageFactory::new()->count(3)->create(['event_id' => $event->id]);
        });

        TicketTypeFactory::new()->count(5)->create();

        PurchaseFactory::new()->count(20)->create()->each(function ($purchase) {
            TicketFactory::new()->count(5)->create([
                'purchase_id' => $purchase->id,
                'session_id' => $purchase->session_id
            ]);
        });
    }
}
