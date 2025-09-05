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
        Log::debug('Starting city data loading');

       GeoDataFacade::load_cities(config('geo-data.data_source.cities.url') );


    }

    protected function load_cities()
    {
        // Implementation for loading cities
        Log::debug('Starting city data loading');

        try {

            $fileSource = config('geo-data.data_source.cities.url');

            $records = $this->createDataFromSource($fileSource);

            Log::debug('Data loaded from source: ' . iterator_count($records) . ' records found.');

            foreach ($records as $record) {

                    try {

                        $geoCity = new GeoCity($record);
                        $geoCity->creatSlug();
                        $geoCity->save();


                    } catch (\Exception $e) {
                        Log::error('Failed to insert record: ' . json_encode($record) . ' Error: ' . $e->getMessage());
                    }

                Log::debug('Records created total count: ' . iterator_count($records));
            }

        }catch (\Exception $e) {
            Log::error('Failed to loading data ' . $e->getMessage());

        }

    }

    protected function load_locations()
    {
        // Implementation for loading cities
        Log::debug('Starting location data loading');

        try {

            $fileSource = config('geo-data.data_source.locations.url');

            $records = $this->createDataFromSource($fileSource);

            Log::debug('Data loaded from source: ' . iterator_count($records) . ' records found.');

            foreach ($records as $record) {

                    try {

                        $geoCity = new GeoLocation($record);
                        $geoCity->creatSlug();
                        $geoCity->save();


                    } catch (\Exception $e) {
                        Log::error('Failed to insert record: ' . json_encode($record) . ' Error: ' . $e->getMessage());
                    }
                }

                Log::debug('Records created total count: ' . iterator_count($records));


        }catch (\Exception $e) {
            Log::error('Failed to loading data ' . $e->getMessage());

        }


    }

    protected function createDataFromSource($url)
    {
        // Implementation for creating data from source
        Log::debug('Creating data from source ' . $url);

        $csvPath = $this->downloadAndExtractZip($url);

        if (!file_exists($csvPath)) {

            throw new \Exception('File not found after extraction');
        }

        $records = $this->createRecords($csvPath);

        //unlink($csvPath);

        return $records;
    }
}
