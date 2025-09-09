<?php

namespace Rotaz\GeoData\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rotaz\GeoData\Facades\GeoDataFacade;
use Rotaz\GeoData\Models\GeoCity;
use Rotaz\GeoData\Models\GeoLocation;
use Rotaz\GeoData\Traits\WithDefaultData;

class GeoDataLoader extends Command
{
    use WithDefaultData;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:geo-data-loader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::debug('Starting geo data loading');

        $this->load_cities();
        $this->load_locations();

    }

    protected function load_cities()
    {
        // Implementation for loading cities
        Log::debug('Starting cities data loading');

        try {

            $fileSource = config('geo-data.data_source.cities.url');

            GeoDataFacade::load_cities($fileSource);

        }catch (\Exception $e) {
            Log::error('Failed to loading data ' . $e->getMessage());

        }

    }

    protected function load_locations()
    {
        try {

            $fileSource = config('geo-data.data_source.locations.url');

            GeoDataFacade::load_locations($fileSource);

        }catch (\Exception $e) {
            Log::error('Failed to loading data ' . $e->getMessage());

        }
    }

}
