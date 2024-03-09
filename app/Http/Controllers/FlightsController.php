<?php

namespace App\Http\Controllers;

use App\Http\Resources\AirportResource;
use App\Http\Resources\FlightResource;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FlightsController extends Controller
{
    /**
     * Fetch airports method
     * @return \Illuminate\Http\JsonResponse
     */
    public function airports(Request $request): \Illuminate\Http\JsonResponse
    {
        // query query query query query query
        $query = $request->query('query', '');

        $airports = Airport::query()
            ->where('city', 'ILIKE', $query)
            ->orWhere('iata', 'ILIKE', $query)
            ->orWhere('name', 'ILIKE', $query)
            ->get();

        return response()->json([
            'data' => [
                'items' => AirportResource::collection($airports),
            ],
        ], 200);
    }

    /**
     * Fetch all flights for date1-date2 pairs
     */
    public function flights(Request $request): JsonResponse {
        $request->validate([
            'from' => 'required|string|min:3|max:3',
            'to' => 'required|string|min:3|max:3',
            'date1' => 'required|date_format:Y-m-d',
            'date2' => 'date_format:Y-m-d',
            'passengers' => 'required|min:1|max:8'
        ]);

        $from = $request->input('from');
        $to = $request->input('to');

        $date1 = $request->input('date1');
        $date2 = $request->input('date2', null);

        $passengers = $request->integer('passengers', 1);

        // NOTE: INNER JOIN performs as search for `from-to` pairs
        $flights = Flight::query()
            ->join('airports `from`', 'iata', '=',  $from)
            ->join('airports `to`', 'iata', '=', $to)
            ->get();

        $flightsTo = [];
        // NOTE: if date2 is not provided - this array must be leave empty
        $flightsBack = [];

        foreach($flights as $flight) {
            // NOTE: all bookings were made to satisfy places count. Do not show in result list
            if($flight->availablePlacesCount() <= $passengers) {
                continue;
            }

            $flightsTo[] = $flight;

            if($date2 != null) {
                $flightsBack[] = $flight;
            }
        }

        return response()->json([
            'data' => [
                'flights_to' => FlightResource::collection($flightsTo)->withDate($date1),
                'flights_back' => FlightResource::collection($flightsBack)->withDate($date2),
            ],
        ], 200);
    }
}
