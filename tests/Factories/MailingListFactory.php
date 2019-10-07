<?php

use Tests\Models\MailingList;
use Faker\Generator as Faker;

$factory->define(MailingList::class, function (Faker $faker) {
    return [
        'name' => $faker->city,
    ];
});
