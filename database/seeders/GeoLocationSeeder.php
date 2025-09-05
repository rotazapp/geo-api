<?php

namespace Database\Seeders;

use App\Models\GeoLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Rotaz\GeoData\Models\GeoCity;
use Rotaz\GeoData\Traits\WithDefaultData;
use ZipArchive;
use function Symfony\Component\String\s;

class GeoLocationSeeder extends Seeder
{
    use WithDefaultData;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::debug('Starting GeoLocationSeeder');

        $zipUrl = config('geo-data.data_source.locations.url');

        $csvPath = $this->downloadAndExtractZip($zipUrl);

        if (!file_exists($csvPath)) {

            throw new \Exception('CSV file not found after extraction');
        }

        $records = $this->createRecords($csvPath);

        foreach ($records as $record) {
            try {
                Log::debug('Seeding from CSV: ', [$record]);


                $geoLocation = new GeoLocation($record);
                $geoLocation->creatSlug();
                $geoLocation->save();


            } catch (\Exception $e) {
                Log::error('Failed to insert record: ' . json_encode($record) . ' Error: ' . $e->getMessage());
            }
        }

        Log::debug('Records created total count: ' . iterator_count($records));

    }

}
