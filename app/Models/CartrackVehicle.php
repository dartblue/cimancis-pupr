<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartrackVehicle extends Model
{
    //
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cartrack_vehicles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'terminal_id',
        'terminal_serial',
        'registration',
        'vehicle_name',
        'manufacturer',
        'model',
        'model_year',
        'colour',
        'chassis_number',
    ];
}
