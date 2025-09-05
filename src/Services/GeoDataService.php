<?php

namespace Rotaz\GeoData\Services;

use Cassandra\Exception\ValidationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Rotaz\GeoData\Exceptions\GeoDataException;
use Rotaz\GeoData\Models\GeoCity;
use Rotaz\GeoData\Models\GeoLocation;
use Rotaz\GeoData\Providers\ViaCepProvider;

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
    public function searchByCep(string $cep): ?GeoLocation
    {
        Log::debug('Searching for CEP: ' . $cep);

        $cep = $this->withRules(data: $cep , field: 'cep', rule: 'required|min:8|max:8|string|regex:/^\d{5}-?\d{3}$/');

        $geoLocation = GeoLocation::where('cep', $cep)->first();

        $can_search = config('geo-data.enable_zip_api_search');

        if (!$geoLocation && $can_search) {
            Log::debug('Search CEP on service : ' . $cep);
            $viaCepData = $this->viaCepProvider->searchByCep($cep);

            if ($viaCepData) {
                Log::debug('CEP found on service : ', [ $viaCepData ]);
                $geoLocation = GeoLocation::updateOrCreate(
                    ['cep' => $cep],
                    [
                        'municipio' => mb_strtoupper($viaCepData['localidade']),
                        'localidade' => mb_strtoupper($viaCepData['bairro']),
                        'uf' => $viaCepData['uf'],
                        'slug' => str_clip($viaCepData['localidade']. '-'. $viaCepData['uf'], slug: true),
                        'ibge' => $viaCepData['ibge'],
                        'logradouro' => mb_strtoupper( $viaCepData['logradouro']) ?? null,
                    ]
                );
            }else{
                Log::debug('CEP not found on service : ' . $cep);
            }
        }

        if( empty($geoLocation) ) {
            throw GeoDataException::validationError('Invalid CEP or data not found.');
        }

        return $geoLocation;
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

    /**
     * @throws ValidationException
     */
    public function searchStreet(string $street)
    {
        $street = $this->withRules(data: $street , field: 'street', rule: 'required|min:8|max:100|string|regex:/^[\p{L}\p{N}\s]+$/u');

        return GeoLocation::where('logradouro','like', "%{$street}%")->limit(50)->get();
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
}
