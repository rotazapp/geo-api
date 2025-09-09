<?php

namespace Rotaz\GeoData\Controllers;

use Illuminate\Http\Request;
use Rotaz\GeoData\Exceptions\GeoDataException;
use Rotaz\GeoData\Facades\GeoDataFacade;
use Rotaz\GeoData\Resources\GeoLocationResource;

class GeoDataController
{
    /**
     * Show the form for creating the resource.
     */
    public function create(): never
    {
        abort(404);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function store(Request $request): never
    {
        abort(404);
    }

    /**
     * Display the resource.
     */
    public function searchCEP($cep): \Illuminate\Http\JsonResponse
    {
        try {

            $geoLocation = GeoDataFacade::searchByCep($cep);
            return response()->json($geoLocation);

        }catch (\Throwable  $e){

            return response()->json(['message' =>$e->getMessage()], 404);
        }

    }

    public function searchCity($city): \Illuminate\Http\JsonResponse
    {
        try {

            $geoLocation = GeoDataFacade::searchCity($city);
            return response()->json($geoLocation);

        }catch (\Throwable  $e){

            return response()->json(['message' =>$e->getMessage()], 404);
        }

    }

    public function searchStreet($street): \Illuminate\Http\JsonResponse
    {
        try {

            $geoLocation = GeoDataFacade::searchStreet($street);
            return response()->json(GeoLocationResource::collection($geoLocation));

        }catch (\Throwable $e){

            return response()->json(['message' =>$e->getMessage()], 404);
        }

    }


    /**
     * Show the form for editing the resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
    }
}
