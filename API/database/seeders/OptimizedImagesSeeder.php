<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OptimizedImage;

class OptimizedImagesSeeder extends Seeder
{
    public function run(): void
    {
        OptimizedImage::factory(60)->create(); // Crea 60 imágenes optimizadas
    }
}
