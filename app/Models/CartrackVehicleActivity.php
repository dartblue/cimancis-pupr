<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartrackVehicleActivity extends Model
{
    //
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cartrack_vehicle_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trip_id',
        'cartrack_vehicle_id',
        'start_timestamp',
        'end_timestamp',
        'trip_duration',
        'trip_duration_seconds',
        'start_location',
        'end_location',
        'start_odometer',
        'end_odometer',
        'trip_distance',
        'max_speed',
        'idle_time',
        'idle_time_seconds',
        'events_idle',
        'start_coordinates_latitude',
        'start_coordinates_longitude',
        'end_coordinates_latitude',
        'end_coordinates_longitude',
    ];
}
