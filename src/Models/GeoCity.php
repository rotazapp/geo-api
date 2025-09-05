<?php

namespace Rotaz\GeoData\Models;

use Illuminate\Database\Eloquent\Model;
use Rotaz\GeoData\Traits\WithDefaultData;

class GeoCity extends Model
{
    protected $fillable = [
        'id',
        'id6',
        'tse',
        'rf',
        'bcb',
        'ddd',
        'slug',
        'municipio',
        'uf',
        'regiao',
        'meso_regiao',
        'microregiao',
        'regiao_imediata',
        'regiao_intermediaria',
        'regiao_saude',
    ];

    protected $table = 'geo_cities';

    public $timestamps = true;

    public function creatSlug(): void
    {
        $this->slug  = str_clip( $this->municipio, slug: true ) . '-' . $this->uf ;
    }
}
