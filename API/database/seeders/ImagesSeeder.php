<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Image;

class ImagesSeeder extends Seeder
{
    public function run(): void
    {
        Image::factory(20)->create(); // Crea 20 imágenes
    }
}
