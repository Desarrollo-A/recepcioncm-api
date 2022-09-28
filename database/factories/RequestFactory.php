<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Request::class, function (Faker $faker) {
    return [
        'title' => $this->faker->company(),
        'comment' => $this->faker->sentence(),
        'add_google_calendar' => $this->faker->boolean,
        'people' => rand(2,10)
    ];
});
