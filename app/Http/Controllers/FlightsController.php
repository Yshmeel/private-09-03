<?php

namespace App\Http\Controllers;

use App\Http\Resources\AirportResource;
use App\Http\Resources\FlightResource;
use App\Models\Airport;
use App\Models\Flight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $airports = Airport::query();

        // NOTE: search with raw query, convert fields to lower case and compare
        if($query != '') {
            $safeQuery = '%' . strtolower($query) . '%';
            $airports = $airports
                ->whereRaw("LOWER(city) LIKE ?", [$safeQuery])
                ->orWhereRaw("LOWER(name) LIKE ?", [$safeQuery])
                ->orWhereRaw("LOWER(iata) LIKE ?", [$safeQuery]);
        }

        $airports = $airports->get();

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
        $date2 = $request->input('date2');

        $passengers = $request->integer('passengers', 1);

        // NOTE: INNER JOIN performs as search for `from-to` pairs
        $flights = Flight::query()
            ->with(['from', 'to'])
            ->join('airports AS from', 'from.id', '=', 'flights.from_id')
            ->join('airports AS to', 'to.id', '=', 'flights.to_id')
            ->whereRaw('(from.iata = ? AND to.iata = ?) OR (from.iata = ? AND to.iata = ?)', [
                $from,
                $to,
                $to,
                $from,
            ])
            ->get();

        $flightsTo = [];
        // NOTE: if date2 is not provided - this array must be leave empty
        $flightsBack = [];

        foreach($flights as $flight) {
            if($flight->availablePlacesCount($date1) < $passengers ) {
                continue;
            }

            // NOTE: is flight in loop is TO destination
            if(
                ($flight->from->iata == $from && $flight->to->iata == $to) &&
                // NOTE: all bookings were made to satisfy places count, if so - not show in result list
                $flight->availablePlacesCount($date1) > $passengers
            ) {
                $flight->date = $date1;
                $flightsTo[] = $flight;
            }

            // NOTE: is flight in loop is FROM destination
            if(
                $date2 != null &&
                ($flight->from->iata == $to && $flight->to->iata == $from) &&
                // NOTE: all bookings were made to satisfy places count, if so - not show in result list
                $flight->availablePlacesCount($date2) > $passengers
            ) {
                $flight->date = $date2;
                $flightsBack[] = $flight;
            }
        }

        return response()->json([
            'data' => [
                'flights_to' => FlightResource::collection($flightsTo),
                'flights_back' => FlightResource::collection($flightsBack),
            ],
        ], 200);
    }
}
