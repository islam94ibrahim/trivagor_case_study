<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->name,
        'api_token' =>  Illuminate\Support\Str::random(80)
    ];
});

$factory->define(App\Location::class, function (Faker\Generator $faker) {
    return [
        'city' => $faker->city,
        'state' => $faker->state,
        'country' => $faker->country,
        'zip_code' => $faker->postcode,
        'address' => $faker->address
    ];
});

$factory->define(App\Item::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'rating' => $faker->numberBetween(0, 5),
        'category' => $faker->randomElement(['hotel', 'alternative', 'hostel', 'lodge', 'resort', 'guest-house']),
        'location_id' => factory(App\Location::class)->create()->id,
        'user_id' => factory(App\User::class)->create()->id,
        'image' => $faker->url,
        'reputation' => $faker->numberBetween(0, 1000),
        'price' => $faker->randomNumber(3),
        'availability' => $faker->randomNumber(1)
    ];
});
