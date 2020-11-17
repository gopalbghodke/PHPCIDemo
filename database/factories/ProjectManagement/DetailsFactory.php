<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\ProjectManagement\Details;

$factory->define(Details::class, function (Faker $faker) {
    return [
        'non_linear_id' => $faker->randomDigitNotNull(),
        'title' => $faker->title,
    ];
});
