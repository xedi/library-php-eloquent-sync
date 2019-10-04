<?php

use Tests\Models\MailingListSubscriber;
use Faker\Generator as Faker;

$factory->define(MailingListSubscriber::class, function (Faker $faker) {
    return [
        'email_address' => $faker->email,
    ];
});
