<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    public $visible = [
        'id',
        'booking',
        'first_name',
        'last_name',
        'birth_date',
        'document_number',
        'place_from',
        'place_back',
        'created_at',
        'updated_at'
    ];

    public function booking() {
        return $this->belongsTo(Booking::class);
    }
}
