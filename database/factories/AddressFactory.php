<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'street' => $faker->streetAddress,
        'num_ext' => $faker->randomNumber(3),
        'suburb' => $faker->domainName,
        'postal_code' => $faker->randomNumber(5),
        'state' => $faker->state
    ];
});
