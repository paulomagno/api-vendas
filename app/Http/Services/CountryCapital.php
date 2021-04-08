<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;

class CountryCapital {

    private $response = NULL;

    // Returns the capital of a country
    public static function getCountryCapital($capitalName) {

        return Http::get(env('API_URL').$capitalName)->json();

    } // end getCountryCapital
}
