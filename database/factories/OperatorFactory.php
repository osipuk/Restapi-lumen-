<?php

$factory->define(App\Models\Operator::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->lastName, // sometimes ->company too long
        'country' => factory('App\Models\Country')->create(),
        'headOperator' => factory('App\Models\HeadOperator')->create(),
        // 'mobileNetworks' => factory('App\Models\mobileNetwork')->create(),
    ];
});
