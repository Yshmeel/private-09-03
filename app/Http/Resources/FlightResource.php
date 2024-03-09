<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
{
    public $date = '';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'flight_id' => $this->id,
            'flight_code' => $this->flight_code,
            'from' => [
                'city' => $this->from->city,
                'airport' => $this->from->airport,
                'iata' => $this->from->iata,
                'date' => $this->date,
                'time' => $this->time_from,
            ],
            'to' => [
                'city' => $this->to->city,
                'airport' => $this->to->airport,
                'iata' => $this->to->iata,
                'date' => $this->date,
                'time' => $this->time_to,
            ],
            'cost' => $this->cost,
            'availability' => $this->availablePlacesCount(),
        ];
    }

    public function withDate(string $date): FlightResource {
        $this->date = $date;
        return $this;
    }
}
