<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    public $visible = [
        'id',
        'flight_code',
        'from',
        'to',
        'time_from',
        'time_to',
        'cost',
        'created_at',
        'updated_at'
    ];

    public function from() {
        return $this->belongsTo(Airport::class);
    }

    public function to() {
        return $this->belongsTo(Airport::class);
    }
}
