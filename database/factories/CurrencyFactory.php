<?php 

$factory->define(App\Models\Currency::class, function (Faker\Generator $faker) {
    return [
       'name'=> $faker->currencyCode,
       'symbol' => '$',
       'euroRelation'=> $faker->randomFloat(null,0,5),
       'usdRelation'=> $faker->randomFloat(null,0,5),
    ];
});
