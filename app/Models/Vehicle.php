<?php

namespace App\Models;

use App\Models\VehiclePosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'terminal_id',
        'registration',
        'manufacturer',
        'model',
        'model_year',
        'colour',
        'chassis_number'
    ];

    public function positions()
    {
        return $this->hasMany(VehiclePosition::class, 'vehicle_id', 'vehicle_id');
    }
}
