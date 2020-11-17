<?php

use Faker\Generator as Faker;
use Brunoocto\Sample\Models\ProjectManagement\Comments;

$factory->define(Comments::class, function (Faker $faker) {
    return [
        'other_type' => $faker->randomElement(['projects', 'tasks', 'files', 'comments']),
        'other_id' => $faker->randomDigitNotNull(),
        'content' => $faker->text,
    ];
});
