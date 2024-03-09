<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Passenger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TODO-list:
 * 1) fix exception handler for validation (test errors bag)
 * 2) test $request->json in booking
 */

class BookingController extends Controller
{
    /**
     * Create new bookings for passengers
     * @return void
     */
    public function post(Request $request) {
        $request->validate([
            'flight_from' => 'required',
            'flight_from.id' => 'required|number|exists:Flight',
            'flight_from.date' => 'required|date_format:Y-m-d',
            'flight_to.id' => 'required|number|exists:Flight',
            'flight_to.date' => 'required|date_format:Y-m-d',

            // Passengers validation
            'passengers' => 'required|array',
            'passengers.first_name' => 'required|string',
            'passengers.last_name' => 'required|string',
            'passengers.birth_date' => 'required|string',
            'passengers.document_number' => 'required|string|min:10|max:10',
        ]);

        $flightFrom = $request->json('flight_from');
        $flightTo = $request->json('flight_to');
        $passengers = $request->json('passengers');

        dd($flightFrom, $flightTo);
    }

    public function get(Request $request, string $code): JsonResponse {
        $booking = Booking::query()
            ->with([
                'flightFrom.from',
                'flightFrom.to',
                'flightBack.from',
                'flightBack.to',
                'passengers',
            ])
            ->where('code', $code)->firstOrFail();

        return response()->json([
            'data' => new BookingResource($booking),
        ], 200);
    }

    public function getOccupiedSeats(Request $request, string $code): JsonResponse {
        $booking = Booking::query()
            ->with(['passengers'])
            ->where('code', $code)->firstOrFail();

        $occupiedFrom = [];
        $occupiedTo = [];

        // NOTE: place_from and place_to is nullable by default,
        // NOTE: but user can choose seat through other method
        foreach($booking->passengers as $passenger) {
            if($passenger->place_from != null) {
                $occupiedFrom[] = [
                    'passenger_id' => $passenger->id,
                    'place' => $passenger->place_from
                ];
            }

            if($passenger->place_to != null) {
                $occupiedTo[] = [
                    'passenger_id' => $passenger->id,
                    'place' => $passenger->place_to
                ];
            }
        }

        return response()->json([
            'data' => [
                'occupied_from' => $occupiedFrom,
                'occupied_to' => $occupiedTo,
            ]
        ], 200);
    }

    public function getUserBookings(Request $request) {
        $user = auth()->user();

        $passengers = Passenger::query()
            ->with(['booking'])
            ->where('document_number', $user->document_number)
            ->get();

        // NOTE: starts from passengers and get user bookings by document number
        // NOTE: then just parse bookings from passengers

        return response()->json([
            'data' => [
                'items' => new BookingResource($passengers->map(function ($f) {
                    return $f->booking;
                }))
            ]
        ], 200);
    }
}
