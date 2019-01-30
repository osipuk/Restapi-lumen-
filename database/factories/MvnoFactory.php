<?php

$factory->define(App\Models\Mvno::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->lastName, // sometimes ->company too long
        // 'mobileNetworks' => factory('App\Models\mobileNetwork')->create(),
    ];
});
