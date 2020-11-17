<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\ProjectManagement\Users;

$factory->define(Users::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});
