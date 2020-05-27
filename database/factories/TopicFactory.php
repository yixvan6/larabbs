<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Topic::class, function (Faker $faker) {
    $sentence = $faker->sentence();

    //随机取一个月内的时间
    $updated_at = $faker->dateTimeThisMonth();

    // 创建时间要 小于等于 更新时间
    $created_at = $faker->dateTimeThisMonth($updated_at);

    return [
        'title' => $faker->sentence(),
        'body' => $faker->text(),
        'excerpt' => substr($sentence, 0, 30),
        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
