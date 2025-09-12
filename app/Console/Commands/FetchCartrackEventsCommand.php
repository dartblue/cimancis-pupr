<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchCartrackEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cartrack:fetch-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest Cartrack events and store to DB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        \App\Jobs\FetchCartrackEventsJob::dispatch();
        $this->info('Cartrack events job dispatched at ' . now());
    }
}
