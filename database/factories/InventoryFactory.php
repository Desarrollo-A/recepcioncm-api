<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Inventory::class, function (Faker $faker) {
    return [
        'name' => $this->faker->word(),
        'description' => $this->faker->sentence(),
        'trademark' => $this->faker->company(),
        'stock' => $this->faker->randomDigitNot(0),
        'minimum_stock' => 1,
        'status' => true,
        'image' => \App\Models\Inventory::IMAGE_DEFAULT
    ];
});
