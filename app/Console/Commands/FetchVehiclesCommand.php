<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchVehiclesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:vehicles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch list kendaraan dari Cartrack API dan simpan ke database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Mulai fetch data kendaraan dari Cartrack...');

        try {
            $page = 1;

            do {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . config('services.cartrack.token'),
                ])->get("https://fleetapi-id.cartrack.com/rest/vehicles", [
                    'page' => $page,
                    'per_page' => 10,
                ]);

                if ($response->failed()) {
                    $this->error('Gagal fetch data dari Cartrack: ' . $response->body());
                    return Command::FAILURE;
                }

                $data = $response->json();

                foreach ($data['data'] as $vehicle) {
                    Vehicle::updateOrCreate(
                        ['vehicle_id' => $vehicle['vehicle_id']],
                        [
                            'terminal_id'   => $vehicle['terminal_id'],
                            'registration'  => $vehicle['registration'],
                            'manufacturer'  => $vehicle['manufacturer'],
                            'model'         => $vehicle['model'],
                            'model_year'    => $vehicle['model_year'],
                            'colour'        => $vehicle['colour'],
                            'chassis_number' => $vehicle['chassis_number'],
                        ]
                    );
                }

                $this->info("Page {$page} selesai diproses...");

                $page++;
                $lastPage = $data['meta']['last_page'] ?? 1;
            } while ($page <= $lastPage);

            $this->info('Selesai sync semua kendaraan 🚗');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
