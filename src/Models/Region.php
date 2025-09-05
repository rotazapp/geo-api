<?php

namespace Rotaz\GeoData\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Region extends Model
{
    use Sushi;

    protected $rows = [
        ['code' => '0', 'name' => 'REGIAO 0', 'ABRANGENCIA' => 'GRANDE SP'],
        ['code' => '1', 'name' => 'REGIAO 1', 'ABRANGENCIA' => 'INTERIOR SP'],
        ['code' => '2', 'name' => 'REGIAO 2', 'ABRANGENCIA' => 'RIO DE JANEIRO E ESPIRITO SANTO'],
        ['code' => '3', 'name' => 'REGIAO 3', 'ABRANGENCIA' => 'MINAS GERAIS'],
        ['code' => '4', 'name' => 'REGIAO 4', 'ABRANGENCIA' => 'BAHIA E SERGIPE'],
        ['code' => '5', 'name' => 'REGIAO 5', 'ABRANGENCIA' => 'PERNAMBUCO, ALAGOAS , PARAIBA E RIO GRANDE DO NORTE'],
        ['code' => '6', 'name' => 'REGIAO 6', 'ABRANGENCIA' => 'CEARA, PIAUI, MARANHAO, PARA, AMAPA, AMAZONAS, RORAIMA E ACRE'],
        ['code' => '7', 'name' => 'REGIAO 7', 'ABRANGENCIA' => 'BRASILIA'],
        ['code' => '8', 'name' => 'REGIAO 8', 'ABRANGENCIA' => 'PARANA E SANTA CATARINA'],
        ['code' => '9', 'name' => 'REGIAO 9', 'ABRANGENCIA' => 'RIO GRANDE DO SUL'],
    ];




}