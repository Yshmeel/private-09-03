<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassengerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birth_date' => Carbon::createFromTimeString($this->birth_date)->format('Y-m-d'),
            'document_number' => $this->document_number,
            'place_from' => $this->place_from ?? null,
            'place_to' => $this->place_to ?? null,
        ];
    }
}
