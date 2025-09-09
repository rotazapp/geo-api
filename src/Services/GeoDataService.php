<?php

namespace Rotaz\GeoData\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Rotaz\GeoData\Exceptions\GeoDataException;
use Rotaz\GeoData\Models\GeoCity;
use Rotaz\GeoData\Models\GeoLocation;
use Rotaz\GeoData\Providers\ViaCepProvider;
use Rotaz\GeoData\Resources\GeoLocationResource;

class GeoDataService
{
    protected $viaCepProvider;

    public function __construct(ViaCepProvider $viaCepProvider)
    {
        $this->viaCepProvider = $viaCepProvider;
    }

    /**
     * @throws GeoDataException
     * @throws ValidationException
     */
    public function searchByCep(string $cep): ?GeoLocationResource
    {
        Log::debug('Searching for CEP: ' . $cep);

        $cep = $this->withRules(data: $cep , field: 'cep', rule: 'required|min:8|max:8|string|regex:/^\d{5}-?\d{3}$/');

        $geoLocation = $this->findCEP($cep);

        $can_search = config('geo-data.enable_zip_api_search');

        if (!$geoLocation && $can_search) {
            Log::debug('Search CEP on service : ' . $cep);
            $viaCepData = $this->viaCepProvider->searchByCep($cep);

            if ($viaCepData) {
                Log::debug('CEP found on service : ', [ $viaCepData ]);
                if( Schema::hasTable('geo_location')){
                    $geoLocation = GeoLocation::updateOrCreate(
                        ['cep' => $cep],
                        [
                            'municipio' => mb_strtoupper($viaCepData['localidade']),
                            'bairro' => mb_strtoupper($viaCepData['bairro']),
                            'uf' => $viaCepData['uf'],
                            'slug' => str_clip($viaCepData['localidade'] , slug: true ) . '-'. $viaCepData['uf'],
                            'logradouro' => mb_strtoupper( $viaCepData['logradouro']) ?? null,
                        ]
                    );
                }else{
                    Log::debug('Table geo_location does not exist. Skipping database save.');
                    $geoLocation = (object)[
                        'cep' => $cep,
                        'municipio' => mb_strtoupper($viaCepData['localidade']),
                        'bairro' => mb_strtoupper($viaCepData['bairro']),
                        'uf' => $viaCepData['uf'],
                        'slug' => str_clip($viaCepData['localidade'] , slug: true ) . '-'. $viaCepData['uf'],
                        'logradouro' => mb_strtoupper( $viaCepData['logradouro']) ?? null,
                    ];

                }

            }else{
                Log::debug('CEP not found on service : ' . $cep);
            }
        }

        if( empty($geoLocation) ) {
            throw GeoDataException::validationError('Invalid CEP or data not found.');
        }

        return new GeoLocationResource($geoLocation);
    }

    /**
     * @throws ValidationException
     */
    public function searchCity(string $city)
    {
        Log::debug('Searching for CITY: ' . $city);
        $city = $this->withRules(data: $city , field: 'city', rule: 'required|min:8|max:100|string|regex:/^[\p{L}\p{N}\s]+$/u');
        $city = str_clip($city, slug: true);

        return GeoCity::where('slug','like', "%{$city}%")->limit(50)->get();
    }

    public function findCEP(string $cep)
    {
        if( empty($cep) ){
            return null;
        }

        if( !Schema::hasTable('geo_location')){
            return null;
        }
        return GeoLocation::where('cep', $cep)->first();

    }

    /**
     * @throws ValidationException
     */
    public function searchStreet(string $street)
    {
        $street = $this->withRules(data: $street , field: 'street', rule: 'required|min:8|max:100|string|regex:/^[\p{L}\p{N}\s]+$/u');

        return GeoLocationResource::collection(GeoLocation::where('logradouro','like', "%{$street}%")->limit(50)->get());
    }

    /**
     * @throws ValidationException
     */
    protected function withRules(string $data , string $field , string $rule) : mixed
    {
        $result =  Validator::make([$field => $data], [
            $field => $rule,
        ])->validate();

        return Arr::first($result);

    }

    public function load_cities($fileSource)
    {
        // Implementation for loading cities
        Log::debug('Starting city data loading');

        try {

            $csvPath = download_extract($fileSource);
            $this->createRecords($csvPath , GeoCity::class);

        } catch (\Exception $e) {
            Log::error('Error loading city data: ' . $e->getMessage());
        }

    }

    public function load_locations($fileSource)
    {

        Log::debug('Starting locations data loading');

        try {

            $csvPath = download_extract($fileSource);
            $this->createRecords($csvPath , GeoLocation::class);

        } catch (\Exception $e) {
            Log::error('Error loading locations data: ' . $e->getMessage());
        }

    }

    public function createRecords(string $csvPath, $model): void
    {
        Log::debug('Creating records from CSV: ' . $csvPath);
        $reader = \League\Csv\Reader::createFromPath($csvPath, 'r');
        $reader->setHeaderOffset(0); // Assuming the first row contains headers
        $header = $reader->getHeader(); // Get the header row
        Log::debug('CSV Header: ' . implode(', ', $header));
        $records = $reader->getRecords();
        Log::debug('Total records found: ' . iterator_count($records));
        foreach ($records as $record) {
            try {
                $record['slug'] = str_clip($record['municipio']. '-'. $record['uf'], slug: true);
                $model::create($record);
            } catch (\Exception $e) {
                Log::error('Error saving record: ' . $e->getMessage());
            }
        }
        Log::debug('Records saved ');

        unlink($csvPath);

    }

}
