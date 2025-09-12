<?php

namespace App\Jobs;

use App\Models\VehiclePosition;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchCartrackEventsJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://fleetapi-id.cartrack.com/rest/vehicles/events', [
            'headers' => [
                'Authorization'     => 'Bearer ' . config('services.cartrack.token'),
            ],
            'query' => [
                'start_timestamp'   => now()->subMinutes(5)->format('Y-m-d H:i:s'),
                'end_timestamp'     => now()->format('Y-m-d H:i:s'),
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        foreach ($data['data'] ?? [] as $event) {
            VehiclePosition::updateOrCreate(
                ['vehicle_id' => $event['vehicle_id']],
                [
                    'registration'      => $event['registration'] ?? null,
                    'latitude'          => $event['location']['latitude'] ?? 0,
                    'longitude'         => $event['location']['longitude'] ?? 0,
                    'event_description' => $event['event_description'] ?? null,
                    'event_ts'          => $event['event_ts'] ?? now(),
                ]
            );
        }
    }
}
