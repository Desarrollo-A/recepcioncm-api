<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\RequestRoom::class, function (Faker $faker) {
    return [
        'external_people' => rand(0,2)
    ];
});
