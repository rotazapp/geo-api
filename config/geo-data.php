<?php

return [
    'default_country' => env('GEODATA_DEFAULT_COUNTRY', 'BRL'),
    'default_language' => env('GEODATA_DEFAULT_LANGUAGE', 'pt_BR'),
    'enable_zip_api_search' => env('GEODATA_ENABLE_ZIP_API_SEARCH', true),
    'data_source' => [
        'cities' => [
            'url' => env('GEODATA_CITIES_URL', 'https://storage.googleapis.com/rotaz-data-open/geo-municipio.zip'),
        ],
        'locations' => [
            'url' => env('GEODATA_LOCATION_URL', 'https://storage.googleapis.com/rotaz-data-open/geo-cep.zip'),
        ],
    ]
];
