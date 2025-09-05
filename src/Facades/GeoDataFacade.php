<?php

namespace Rotaz\GeoData\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static searchByCep($cep)
 * @method static searchCity($city)
 * @method static searchStreet($street)
 */
class GeoDataFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'GeoDataService';
    }
}
