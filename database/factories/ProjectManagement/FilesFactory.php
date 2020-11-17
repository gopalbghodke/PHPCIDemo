<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\ProjectManagement\Files;

$factory->define(Files::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
        'mime' => $faker->mimeType,
        'bytes' => $faker->numberBetween(1000000, 9999999),
        'path' => $faker->url,
        'width' => $faker->numberBetween(720, 1920),
        'height' => $faker->numberBetween(720, 1920),
    ];
});
