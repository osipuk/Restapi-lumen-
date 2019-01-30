<?php

$factory->define(App\Models\HeadOperator::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->lastName, // sometimes ->company too long
        // 'operator' => factory('App\Models\Operator')->create(),
    ];
});
