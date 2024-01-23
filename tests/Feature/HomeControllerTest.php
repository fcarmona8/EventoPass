<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Event;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testHomeControllerIndex()
    {
        // Preparación de datos de prueba
        $category = Category::factory()->create();
        $event = Event::factory()->create(['category_id' => $category->id]);

        // Simula una solicitud GET a la ruta principal
        $response = $this->get('/');

        // Asegura que la respuesta tenga un código de estado 200 (OK)
        $response->assertStatus(200);

        // Asegura que la vista tiene las variables esperadas
        $response->assertViewHas('selectedFiltro');
        $response->assertViewHas('searchTerm');
        $response->assertViewHas('selectedCategoria');
        $response->assertViewHas('categories');
        $response->assertViewHas('categoriesPerPage');
        $response->assertViewHas('events');

        // Asegura que la vista muestra la categoría y el evento creados
        $response->assertSeeText($category->name);
        $response->assertSeeText($event->name);
    }

    

    public function testHomeControllerIndexWithEvents()
    {
        // Preparación de datos de prueba
        $category = Category::factory()->create();
        $event1 = Event::factory()->create(['category_id' => $category->id]);
        $event2 = Event::factory()->create(['category_id' => $category->id]);

        // Simula una solicitud GET a la ruta principal
        $response = $this->get('/');

        // Asegura que la respuesta tenga un código de estado 200 (OK)
        $response->assertStatus(200);

        // Asegura que la vista muestra los eventos creados
        $response->assertSeeText($event1->name);
        $response->assertSeeText($event2->name);
    }

}
