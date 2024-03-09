<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    public $_availablePlacesCount = null;

    public $visible = [
        'id',
        'flight_code',
        'from',
        'to',
        'time_from',
        'time_to',
        'cost',
        'places_count',
        'created_at',
        'updated_at'
    ];

    public function from() {
        return $this->belongsTo(Airport::class);
    }

    public function to() {
        return $this->belongsTo(Airport::class);
    }

    /**
     * Method counts available places count for flight
     * @return int
     */
    public function availablePlacesCount(string $date) {
        $bookings = Booking::query()
            ->where('flight_from', $this->id)
            ->orWhere('flight_back', $this->id)
            ->where('date_from', $date)
            ->orWhere('date_back', $date)
            ->get();

        $count = $this->places_count - count($bookings);
        return $count;
    }

}
