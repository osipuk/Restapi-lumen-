<?php

$factory->define(App\Models\Country::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->countryCode, // sometimes ->country too long
        'iso2' => $faker->countryCode,
        'iso3' => $faker->countryCode,
            'mcc' => '205',
            'continent' => 'North America',
            'currency' => factory('App\Models\Currency')->create(),
            'phonePrefix' => $faker->unique()->randomDigit,
    ];
});
