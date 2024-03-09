<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Airport;
use App\Models\Flight;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $airport1 = Airport::create([
            'city' => 'Astana',
            'name' => 'Astana',
            'iata' => 'NQZ'
        ]);

        $airport2 = Airport::create([
            'city' => 'Almaty',
            'name' => 'Almaty',
            'iata' => 'ALA'
        ]);

        $airport3 = Airport::create([
            'city' => 'Oral',
            'name' => 'Oral',
            'iata' => 'URA'
        ]);

        Flight::create([
            'flight_code' => 'F1',
            'from_id' => $airport1->id,
            'to_id' => $airport2->id,
            'time_from' => '10:30',
            'time_to' => '12:45',
            'cost' => 10000,
            'places_count' => 48
        ]);

        Flight::create([
            'flight_code' => 'F2',
            'from_id' => $airport2->id,
            'to_id' => $airport1->id,
            'time_from' => '18:45',
            'time_to' => '19:30',
            'cost' => 10000,
            'places_count' => 48
        ]);

        Flight::create([
            'flight_code' => 'F3',
            'from_id' => $airport3->id,
            'to_id' => $airport1->id,
            'time_from' => '09:10',
            'time_to' => '14:20',
            'cost' => 3000,
            'places_count' => 64
        ]);

        Flight::create([
            'flight_code' => 'F4',
            'from_id' => $airport3->id,
            'to_id' => $airport2->id,
            'time_from' => '13:15',
            'time_to' => '16:40',
            'cost' => 8000,
            'places_count' => 96
        ]);

        Flight::create([
            'flight_code' => 'F5',
            'from_id' => $airport2->id,
            'to_id' => $airport3->id,
            'time_from' => '19:15',
            'time_to' => '21:40',
            'cost' => 8000,
            'places_count' => 96
        ]);
    }
}
