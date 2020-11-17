<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\ProjectManagement\Milestones;

$factory->define(Milestones::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
    ];
});
