<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Package;
use Faker\Generator as Faker;

$factory->define(Package::class, function (Faker $faker) {
    return [
        'name_receive' => $faker->name,
        'email_receive' => $faker->safeEmail,
    ];
});
