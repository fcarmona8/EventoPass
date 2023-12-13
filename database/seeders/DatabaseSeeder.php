<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Category::factory()->count(10)->create();
        Venue::factory()->count(10)->create();
        Event::factory()->count(30)->create();
    }
}
