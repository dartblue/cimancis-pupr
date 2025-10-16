<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartrackVehicle;
use App\Models\CartrackVehicleActivity;

class CartrackActivityController extends Controller
{
    public function index()
    {
        $last_sync = '';
        $cartrack_activities = CartrackVehicleActivity::all();
        if ($cartrack_activities) {
            $last_sync = $cartrack_activities->max('created_at');
        }
        return view('cartrack-activity.index', compact('cartrack_activities', 'last_sync'));
    }

    public function getCartrackVehicles()
    {
        $cartrack_vehicles = CartrackVehicle::with([
            'heavyEquipment',
            'latestActivity'
        ])->get();
        return response()->json($cartrack_vehicles);
    }
}
