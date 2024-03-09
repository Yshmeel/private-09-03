<?php

namespace App\Http\Resources;

use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // NOTE: cost is pricing for all flights. flightFrom/flightTo is always provided
        $cost = $this->flightFrom->cost;

        // NOTE: assign from/back date to model, to satisfy flight resource
        $this->flightFrom->date = $this->date_from;
        $this->flightBack->date = $this->date_back;

        $flights = [
            new FlightResource($this->flightFrom),
        ];

        // NOTE: flightTo might be null
        if($this->flightBack != null) {
            $flights[] = new FlightResource($this->flightBack);
            $cost += $this->flightBack->cost;
        }

        return [
            'code' => $this->code,
            'cost' => $cost,
            'flights' => $flights,
            'passengers' => $this->passengers->map(function(Passenger $p) {
                return new PassengerResource($p);
            })
        ];
    }
}
