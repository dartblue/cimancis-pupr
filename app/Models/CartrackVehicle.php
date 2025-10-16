<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the cartrackVehicleActivity that owns the CartrackVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cartrackVehicleActivity(): BelongsTo
    {
        return $this->belongsTo(CartrackVehicleActivity::class, 'foreign_key', 'other_key');
    }

    /**
     * Get the latestActivity associated with the CartrackVehicle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestActivity(): HasOne
    {
        return $this->hasOne(CartrackVehicleActivity::class, 'cartrack_vehicle_id', 'vehicle_id')
            ->latestOfMany();
    }

    public function heavyEquipment()
    {
        return $this->morphedByMany(
            HeavyEquipment::class,
            'integratable',
            'heavy_equipment_integrations',
            'integratable_id',
            'heavy_equipment_id'
        );
    }
}
