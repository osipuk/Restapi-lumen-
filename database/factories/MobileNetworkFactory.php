<?php

$factory->define(App\Models\mobileNetwork::class, function (Faker\Generator $faker) {
    return [
        'mccmnc' => $faker->shuffle('12345'),
        'operator' => factory('App\Models\Operator')->create(),
        'mvno' => factory('App\Models\Mvno')->create(),
    ];
});
