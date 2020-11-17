<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\Sample;

$factory->define(Sample::class, function (Faker $faker) {
    return [
        'text' => $faker->sentence,
    ];
});
