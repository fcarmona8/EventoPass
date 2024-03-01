<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OptimizedImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $imageSizes = ['petit', 'mitjà', 'gran'];
        
        $size = $this->faker->randomElement($imageSizes);
        $width = $size === 'petit' ? 640 : ($size === 'mitjà' ? 1280 : 1920);
        $height = $size === 'petit' ? 480 : ($size === 'mitjà' ? 720 : 1080);

        return [
            'image_id' => Image::factory(),
            'version' => $size,
            'path' => $this->faker->imageUrl($width, $height, 'animals', true),
            'url' => $this->faker->imageUrl($width, $height, 'animals', true),
        ];
    }
}
