<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartrackVehicle;
use App\Models\CartrackVehicleActivity;
use App\Services\CartrackActivityServices;

class CartrackActivityController extends Controller
{
    protected $cartrackActivityServices;

    public function __construct(CartrackActivityServices $cartrackActivityServices)
    {
        $this->cartrackActivityServices = $cartrackActivityServices;
    }

    public function index()
    {
        $last_sync = '';
        $cartrack_activities = CartrackVehicleActivity::query();
        if ($cartrack_activities) {
            $last_sync = $cartrack_activities->max('created_at');
        }
        $cartrack_activities = $cartrack_activities->paginate(10);
        return view('cartrack-activity.index', compact('cartrack_activities', 'last_sync'));
    }

    public function getCartrackVehicles()
    {
        $cartrack_vehicles = CartrackVehicle::with([
            'heavyEquipment',
            'latestActivity',
            'cartrackVehicleActivity',
        ])->get();
        return response()->json($cartrack_vehicles);
    }

    public function cartrackActivities(Request $request)
    {
        $data = CartrackVehicleActivity::where('cartrack_vehicle_id', $request->vehicleId)
            ->where('start_timestamp', '>=', $request->startDate)
            ->where('end_timestamp', '<=', $request->endDate)
            ->orderBy('start_timestamp', 'asc')
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No activities found.'], 404);
        }

        return response()->json($data);
    }

    public function syncCartrackActivity(Request $request)
    {
        $input = $request->all();
        $result = $this->cartrackActivityServices->syncCartrackActivities($input);

        try {
            //code...
            if ($result['success']) {
                return response()->json([
                    'message' => 'Cartrack activities synced successfully.',
                    'data'  => $request->last_sync
                ]);
            } else {
                return response()->json([
                    'message' => $result['message'],
                ], 500);
            }
        } catch (\Exception $th) {
            //throw $th;
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyinkronkan data: ' . $th->getMessage(),
            ], 500);
        }
    }
}
