<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Product\AttributeGroupModel;
use Faker\Generator as Faker;

$factory->define(AttributeGroupModel::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'sort_order' => rand(1,10),
    ];
});
