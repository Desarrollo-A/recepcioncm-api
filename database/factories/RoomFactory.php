<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Room::class, function (Faker $faker) {
    return [
        'name' => "Sala {$this->faker->safeColorName()}",
        'no_people' => $this->faker->randomDigit(),
    ];
});
