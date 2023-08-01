<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Category\CategoryModel;
use Faker\Generator as Faker;

function getParent(){
    try {
        $cat = CategoryModel::inRandomOrder()->first()->category_id;
    } catch (\Throwable $th) {
        $cat=NULL;
    }
    Log::info($cat);
    return $cat;
}

$factory->define(CategoryModel::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->sentence,
        'parent_id' => getParent(),
        'status' => 1,
        'image' => NULL,
        'sort_order' => rand(1,50),
    ];
});
