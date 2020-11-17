<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\ProjectManagement\NonLinears;

$factory->define(NonLinears::class, function (Faker $faker) {
    return [
        'project_id' => $faker->randomDigitNotNull(),
        'link' => $faker->url,
    ];
});
