<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'no_employee' => $this->faker->numerify('CIB#####'),
        'full_name' => $this->faker->name()." ".$this->faker->lastName(),
        'email' => $this->faker->unique()->freeEmail(),
        'password' => bcrypt('password'),
        'personal_phone' => $this->faker->numerify('##########'),
        'office_phone' => $this->faker->numerify('##########'),
        'position' => strtoupper($this->faker->jobTitle()),
        'area' => strtoupper($this->faker->company())
    ];
});
