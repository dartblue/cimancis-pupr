<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartrackVehicle;
use App\Models\CartrackVehicleActivity;
use App\Services\CartractActivityServices;

class CartrackActivityController extends Controller
{
    protected $cartrackActivityServices;

    public function __construct(CartractActivityServices $cartrackActivityServices)
    {
        $this->cartrackActivityServices = $cartrackActivityServices;
    }

    public function index()
    {
        $last_sync = '';
        $cartrack_activities = CartrackVehicleActivity::all();
        if ($cartrack_activities) {
            $last_sync = $cartrack_activities->max('created_at');
        }
        // dd($last_sync);
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

    public function syncCartrackActivity(Request $request)
    {
        // Simulate syncing process
        // In a real application, you would fetch data from an external API or service
        // and update the CartrackVehicleActivity model accordingly.

        // For demonstration, let's just create a dummy activity

        return response()->json([
            'message' => 'Cartrack activities synced successfully.',
            'data'  => $request->last_sync
        ]);
    }
}
