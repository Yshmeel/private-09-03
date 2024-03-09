<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Http\Resources\PassengerResource;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Passenger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use function Symfony\Component\String\b;

/**
 * TODO-list:
 * 1) fix exception handler for validation (test errors bag) +
 * 2) test $request->json in booking
 * 3) add select seat method
 */

class BookingController extends Controller
{
    /**
     * Create new bookings for passengers
     * @return JsonResponse
     */
    public function post(Request $request) {
        $request->validate([
            'flight_from' => 'required',
            'flight_from.id' => 'required',
            'flight_from.date' => 'required|date_format:Y-m-d',
            'flight_back.id' => 'required',
            'flight_back.date' => 'required|date_format:Y-m-d',

            // Passengers validation
            'passengers' => 'required|array',
            'passengers.0.first_name' => 'required|string',
            'passengers.0.last_name' => 'required|string',
            'passengers.0.birth_date' => 'required|string|date_format:Y-m-d',
            'passengers.0.document_number' => 'required|string|min:10|max:10',
        ]);

        $flightFromObject = (object) $request->json('flight_from');
        $flightBackObject = (object) $request->json('flight_back');

        $passengers = $request->json('passengers');

        $flightFrom = Flight::query()->where('id', $flightFromObject->id)->firstOrFail();
        $flightBack = Flight::query()->where('id', $flightBackObject->id)->firstOrFail();

        $passengersCount = count($passengers);

        if($flightFrom->availablePlacesCount($flightFromObject->date) < $passengersCount) {
            $exception = ValidationException::withMessages([
                'flight_from.date' => 'All places for this flight is already taken'
            ]);

            throw $exception;
        }

        if($flightBack->availablePlacesCount($flightBackObject->date) < $passengersCount) {
            $exception = ValidationException::withMessages([
                'flight_back.date' => 'All places for this flight is already taken'
            ]);

            throw $exception;
        }

        // NOTE: create booking with random code
        $booking = new Booking();

        $booking->flight_from = $flightFrom->id;
        $booking->flight_back = $flightBack->id;
        $booking->date_from = $flightFromObject->date;
        $booking->date_back = $flightBackObject->date;
        $booking->code = Str::upper(Str::random(5));

        $booking->save();

        foreach($passengers as $p) {
            $passenger = new Passenger();
            $passenger->booking_id = $booking->id;
            $passenger->first_name = $p['first_name'];
            $passenger->last_name = $p['last_name'];
            $passenger->birth_date = $p['birth_date'];
            $passenger->document_number = $p['document_number'];
            $passenger->save();
        }

        return response()->json([
            'data' => [
                'code' => $booking->code,
            ],
        ], 201);
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

            if($passenger->place_back != null) {
                $occupiedTo[] = [
                    'passenger_id' => $passenger->id,
                    'place' => $passenger->place_back
                ];
            }
        }

        return response()->json([
            'data' => [
                'occupied_from' => $occupiedFrom,
                'occupied_back' => $occupiedTo,
            ]
        ], 200);
    }

    /**
     * Method for selecting seats in booking
     * @param Request $request
     * @return JsonResponse
     */
    public function selectSeat(Request $request, string $code) {
        $request->validate([
            'passenger' => 'required|integer',
            'seat' => 'required|string|min:2|max:3',
            'type' => 'required|string|in:from,back'
        ]);

        $passenger = $request->input('passenger');
        $seat = $request->input('seat');
        $type = $request->input('type');

        $booking = Booking::query()->where('code', $code)->firstOrFail();

        // NOTE: get other bookings within this flight to not allow to occupy other's seats
        $otherBookings = Booking::query()
            ->select(['id'])
            ->where(
                $type == 'from' ? 'flight_from' : 'flight_back',
                $type == 'from' ? $booking->flight_from : $booking->flight_back,
            )
            ->get(['id'])
            ->map(function($v) { return $v->id; });

        // NOTE: ternary operator decides field to be used in where closure
        $passengerWithSamePlace = Passenger::query()
            ->whereIn('booking_id', $otherBookings)
            ->where($type == 'from' ? 'place_from' : 'place_back', $seat)
            ->whereNot('id', $passenger)
            ->first();

        if($passengerWithSamePlace != null) {
            return response()->json([
                'error' => [
                    'code' => 422,
                    'message' => 'Seat is occupied'
                ],
            ], 422);
        }

        $passenger = Passenger::query()
            ->where('booking_id', $booking->id)
            ->where('id', $passenger)->first();

        // NOTE: user cannot provide invalid passenger id
        if($passenger == null) {
            return response()->json([
                'error' => [
                    'code' => 403,
                    'message' => 'Passenger does not apply to booking'
                ],
            ], 403);
        }

        switch($type) {
            case 'from':
                $passenger->place_from = $seat;
                break;
            case 'back':
                $passenger->place_back = $seat;
                break;
        }

        $passenger->save();

        return response()->json([
            'data' => new PassengerResource($passenger)
        ]);
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
