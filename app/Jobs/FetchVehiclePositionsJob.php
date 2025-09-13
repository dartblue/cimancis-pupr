<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchVehiclePositionsJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $page = 1;

        $yesterday = now()->subDay();

        $startDate = $yesterday->startOfDay()->format('Y-m-d H:i:s');
        $endDate   = $yesterday->endOfDay()->format('Y-m-d H:i:s');

        do {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('services.cartrack.token'),
            ])->get('https://fleetapi-id.cartrack.com/rest/trips', [
                'start_timestamp' => $startDate,
                'end_timestamp' => $endDate,
                'page' => $page,
            ])->json();

            if (empty($response['data'])) {
                break;
            }

            foreach ($response['data'] as $trip) {
                VehiclePosition::updateOrCreate(
                    ['trip_id' => $trip['trip_id']],
                    [
                        'vehicle_id' => $trip['vehicle_id'],
                        'start_latitude' => $trip['start_coordinates']['latitude'] ?? null,
                        'start_longitude' => $trip['start_coordinates']['longitude'] ?? null,
                        'end_latitude' => $trip['end_coordinates']['latitude'] ?? null,
                        'end_longitude' => $trip['end_coordinates']['longitude'] ?? null,
                        'start_timestamp' => $trip['start_timestamp'],
                        'end_timestamp' => $trip['end_timestamp'],
                        'trip_distance' => $trip['trip_distance'],
                    ]
                );
            }

            $page++;
        } while ($page <= $response['meta']['last_page'] ?? 1);
    }
}
