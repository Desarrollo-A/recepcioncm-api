<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Driver;
use Faker\Generator as Faker;

$factory->define(Driver::class, function (Faker $faker) {
    return [
        'no_employee' => $this->faker->numerify('CIB#####'),
        'full_name' => $this->faker->name()." ".$this->faker->lastName(),
        'email' => $this->faker->unique()->freeEmail(),
        'personal_phone' => $this->faker->numerify('##########'),
        'office_phone' => $this->faker->numerify('##########')
    ];
});
