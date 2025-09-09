<?php

namespace Rotaz\GeoData\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GeoLocationResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cep' => $this->cep,
            'logradouro' => mb_strtoupper($this->logradouro),
            'municipio' => mb_strtoupper($this->municipio),
            'bairro' => mb_strtoupper($this->bairro),
            'uf' => mb_strtoupper($this->uf),
            'slug' => $this->slug,
        ];
    }
}
