<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\ProjectManagement\Tasks;

$factory->define(Tasks::class, function (Faker $faker) {
    return [
        'project_id' => $faker->randomDigitNotNull(),
        'title' => $faker->title,
        'content' => $faker->text,
    ];
});
