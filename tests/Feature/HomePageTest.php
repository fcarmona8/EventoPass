<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testHomePageDisplaysEvents()
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $event = Event::first();

        $response->assertSee(e($event->name));
        $response->assertSee(e($event->description));
        $response->assertSee(e($event->main_image));
    }
}