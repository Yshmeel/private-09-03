<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    public $visible = [
        'id',
        'city',
        'name',
        'iata',
        'created_at',
        'updated_at',
    ];
}
