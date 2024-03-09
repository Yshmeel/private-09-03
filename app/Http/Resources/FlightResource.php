<?php

namespace App\Http\Resources;

use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
{
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
                'airport' => $this->from->name,
                'iata' => $this->from->iata,
                'date' => $this->date,
                'time' => substr($this->time_from, 0, strlen($this->time_from) - 3),
            ],
            'to' => [
                'city' => $this->to->city,
                'airport' => $this->to->name,
                'iata' => $this->to->iata,
                'date' => $this->date,
                'time' => substr($this->time_to, 0, strlen($this->time_to) - 3),
            ],
            'cost' => $this->cost,
            'availability' => $this->availablePlacesCount($this->date),
        ];
    }

}
