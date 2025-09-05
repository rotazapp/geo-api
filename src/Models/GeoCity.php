<?php

namespace Rotaz\GeoData\Models;

use Illuminate\Database\Eloquent\Model;
use Rotaz\GeoData\Traits\WithDefaultData;

class GeoCity extends Model
{


    protected $table = 'geo_cities';

    public $timestamps = true;

    public function creatSlug(): void
    {
        $this->slug  = str_clip( $this->nome, slug: true ) . '-' . $this->uf ;
    }
}
