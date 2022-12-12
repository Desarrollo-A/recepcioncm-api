<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Car::class, function (Faker $faker) {
    return [
        'business_name' => $this->faker->company(),
        'trademark' => $this->faker->city(),
        'model' => $this->faker->year(),
        'color' => $this->faker->colorName(),
        'license_plate' => strtoupper(\Illuminate\Support\Str::random(7)),
        'serie' => strtoupper(\Illuminate\Support\Str::random(17)),
        'circulation_card' => $this->faker->randomNumber(7),
        'people' => rand(2,12)
    ];
});
