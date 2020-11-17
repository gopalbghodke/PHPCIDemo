<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\ProjectManagement\Projects;

$factory->define(Projects::class, function (Faker $faker) {
    return [
        'title' => $faker->title,
    ];
});
