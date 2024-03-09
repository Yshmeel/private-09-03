<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $visible = [
        'id',
        'flightFrom',
        'flightBack',
        'date_from',
        'date_back',
        'code',
        'created_at',
        'updated_at'
    ];

    public function flightFrom() {
        return $this->belongsTo(Flight::class, 'flight_from');
    }

    public function flightTo() {
        return $this->belongsTo(Flight::class, 'flight_to');
    }
}
