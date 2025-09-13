<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchVehiclePositionsJob;

class FetchVehiclePositionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:vehicle-positions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch vehicle trips for the last month and store in vehicle_positions table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // Dispatch the job
        FetchVehiclePositionsJob::dispatch();

        $this->info('FetchVehiclePositionsJob dispatched successfully.');
    }
}
