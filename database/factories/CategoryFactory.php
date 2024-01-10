<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define la configuración por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => '',
        ];
    }

    /**
     * Configura la fábrica para crear una categoría de 'Conciertos'.
     */
    public function concerts()
    {
        return $this->state([
            'name' => 'Conciertos',
        ]);
    }

    /**
     * Configura la fábrica para crear una categoría de 'Festivales'.
     */
    public function festivals()
    {
        return $this->state([
            'name' => 'Festivales',
        ]);
    }

    /**
     * Configura la fábrica para crear una categoría de 'Conferencias'.
     */
    public function conferences()
    {
        return $this->state([
            'name' => 'Conferencias',
        ]);
    }

    /**
     * Configura la fábrica para crear una categoría de 'Teatro'.
     */
    public function theatre()
    {
        return $this->state([
            'name' => 'Teatro',
        ]);
    }

    /**
     * Configura la fábrica para crear una categoría de 'Deportes'.
     */
    public function sports()
    {
        return $this->state([
            'name' => 'Deportes',
        ]);
    }
}