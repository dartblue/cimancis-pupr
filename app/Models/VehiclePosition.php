<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'registration',
        'latitude',
        'longitude',
        'event_description',
        'event_ts',
    ];
}
