<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Option\OptionGroupModel;
use Faker\Generator as Faker;

$factory->define(OptionGroupModel::class, function (Faker $faker) {
    return [
        'option_group_name' => $faker->name(),
        'sort_order' => rand(1,10),
    ];
});
