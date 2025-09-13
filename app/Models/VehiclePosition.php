<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclePosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'vehicle_id',
        'registration',
        'chassis_number',
        'terminal_id',
        'terminal_serial',
        'start_timestamp',
        'end_timestamp',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'trip_duration_seconds',
        'idle_time_seconds',
        'trip_distance'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'vehicle_id');
    }
}
