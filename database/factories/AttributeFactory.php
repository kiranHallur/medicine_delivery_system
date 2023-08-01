<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Product\AttributeModel;
use Faker\Generator as Faker;

$factory->define(AttributeModel::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
