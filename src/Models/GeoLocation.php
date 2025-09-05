<?php

namespace Rotaz\GeoData\Models;

use Illuminate\Database\Eloquent\Model;
use Rotaz\GeoData\Traits\WithSushiDriver;

class GeoLocation extends Model
{

    protected $table = 'geo_location';

    protected $fillable = [
        'cep',
        'logradouro',
        'localidade',
        'municipio',
        'slug',
        'uf',
        'ibge',
        'geo',
        'estabelecimentos',
    ];

    public $timestamps = true;

    public function creatSlug(): void
    {
        $this->slug  = str_clip( $this->municipio, slug: true ) . '-' . $this->uf ;
    }
}
