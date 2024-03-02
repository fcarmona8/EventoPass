<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Venue;
use GuzzleHttp\Client;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $client = new Client();

        // Suponiendo que la API espera una solicitud POST con un campo 'image'
        $response = $client->request('POST', config('services.api.url').'/api/V1/images', [
            'headers' => [
                'Accept' => 'application/json',
                'APP-TOKEN' => config('services.api.token'),
            ],
            'multipart' => [
                [
                    'name'     => 'image',
                    'contents' => fopen(public_path('images/default.jpg'), 'r'),
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $imageId = $data['imageId'];

        static $eventCounter = 0;
        $eventNames = ["Evento 1", "Evento 2", "Evento 3",
                       "Evento 4", "Evento 5", "Evento 6",
                       "Evento 7", "Evento 8", "Evento 9",
                       "Evento 10", "Evento 11", "Evento 12",
                       "Evento 13", "Evento 14", "Evento 15",
                       "Evento 16", "Evento 17", "Evento 18",
                       "Evento 19", "Evento 20", "Evento 21",
                       "Evento 22", "Evento 23", "Evento 24",
                       "Evento 25", "Evento 26", "Evento 27",
                       "Evento 28", "Evento 29", "Evento 30"];

        $descriptions = ["Descripción 1", "Descripción 2", "Descripción 3",
                         "Descripción 4", "Descripción 5", "Descripción 6",
                         "Descripción 7", "Descripción 8", "Descripción 9",
                         "Descripción 10", "Descripción 11", "Descripción 12",
                         "Descripción 13", "Descripción 14", "Descripción 15",
                         "Descripción 16", "Descripción 17", "Descripción 18",
                         "Descripción 19", "Descripción 20", "Descripción 21",
                         "Descripción 22", "Descripción 23", "Descripción 24",
                         "Descripción 25", "Descripción 26", "Descripción 27",
                         "Descripción 28", "Descripción 29", "Descripción 30"];

        $images = ["imagen1.jpg", "imagen2.jpg", "imagen3.jpg",
                   "imagen4.jpg", "imagen5.jpg", "imagen6.jpg",
                   "imagen7.jpg", "imagen8.jpg", "imagen9.jpg",
                   "imagen10.jpg", "imagen11.jpg", "imagen12.jpg",
                   "imagen13.jpg", "imagen14.jpg", "imagen15.jpg",
                   "imagen15.jpg", "imagen17.jpg", "imagen18.jpg",
                   "imagen19.jpg", "imagen20.jpg", "imagen21.jpg",
                   "imagen25.jpg", "imagen26.jpg", "imagen27.jpg",
                   "imagen28.jpg", "imagen29.jpg", "imagen30.jpg"];

        $dates = ["2021-01-01", "2021-01-02", "2021-01-03",
                  "2021-01-04", "2021-01-05", "2021-01-06",
                  "2021-01-07", "2021-01-08", "2021-01-09",
                  "2021-01-10", "2021-01-11", "2021-01-12",
                  "2021-01-13", "2021-01-14", "2021-01-15",
                  "2021-01-16", "2021-01-17", "2021-01-18",
                  "2021-01-19", "2021-01-20", "2021-01-21",
                  "2021-01-22", "2021-01-23", "2021-01-24",
                  "2021-01-25", "2021-01-26", "2021-01-27",
                  "2021-01-28", "2021-01-29", "2021-01-30"];

        $eventData = [
            'name' => $eventNames[$eventCounter % count($eventNames)],
            'nominal' => 0,
            'description' => $descriptions[$eventCounter % count($descriptions)],
            'venue_id' => Venue::inRandomOrder()->first()->id ?? Venue::factory(),
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'main_image_id' => $imageId,
            'event_date' => $this->faker->dateTimeBetween(Carbon::tomorrow(), Carbon::today()->addYear())->format('Y-m-d H:i:s')
        ];

        $eventCounter++;
        return $eventData;
    }
}
