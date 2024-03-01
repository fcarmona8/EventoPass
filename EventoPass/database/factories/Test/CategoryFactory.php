<?php

namespace Database\Factories\Test;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }

    public function concerts()
    {
        return $this->state(['name' => 'Conciertos']);
    }

    public function festivals()
    {
        return $this->state(['name' => 'Festivales']);
    }

    public function conferences()
    {
        return $this->state(['name' => 'Conferencias']);
    }

    public function theatre()
    {
        return $this->state(['name' => 'Teatro']);
    }

    public function sports()
    {
        return $this->state(['name' => 'Deportes']);
    }

    public function arts()
    {
        return $this->state(['name' => 'Arte']);
    }

    public function movies()
    {
        return $this->state(['name' => 'Cine']);
    }

    public function music()
    {
        return $this->state(['name' => 'Música']);
    }

    public function dance()
    {
        return $this->state(['name' => 'Danza']);
    }

    public function literature()
    {
        return $this->state(['name' => 'Literatura']);
    }
}
