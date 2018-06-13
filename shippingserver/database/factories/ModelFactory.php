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
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Api\Model\FCLBuyerPostIndex::class, function (Faker\Generator $faker) {

    $serviceIds = [1, 2, 3];
    $serviceNames = ['LCL', 'FCL', 'RORO'];
    $leadTypes = ['spot', 'term'];
    $leadTypeIds = [1, 2];
    $weightUnit = ['mtons', 'tons', 'kgs'];

    return [
        'title' => $faker->sentence(5),
        'lkp_post_id' => $faker->randomNumber(2),
        'lkp_buyer_id' => $faker->randomNumber(1),
        'lkp_service_id' => $faker->randomElement($serviceIds),
        'serviceName' => $faker->randomElement($serviceNames),
        'leadType' => $faker->randomElement($serviceNames),
        'leadTypeName' => $faker->randomElement($leadTypeIds),
        'serviceSubType' => $faker->randomElement($leadTypes),
        'originLocation' => $faker->country,
        'destinationLocation' => $faker->country,
        'lastDateTimeOfQuoteSubmission' => $faker->dateTime,
        'viewCount' => $faker->randomDigit,
        'isPublic' => $faker->boolean(),
        'loadPort' => $faker->randomDigit,
        'dischargePort' => $faker->randomDigit,
        'commodity' => $faker->sentence(),
        'commodityDescription' => $faker->sentence(6),
        'packagingType' => $faker->randomDigit,
        'cargoReadyDate' => $faker->date(),
        'isFumigationRequired' => $faker->boolean(),
        'isFactoryStuffingRequired' => $faker->boolean(),
        'containerType' => $faker->randomDigit,
        'quantity' => $faker->randomDigit,
        'weightUnit' => $faker->randomElement($weightUnit),
        'grossWeight' => $faker->randomFloat(),
        'priceType' => $faker->currencyCode,
        'actualPrice' => $faker->randomFloat(null, 3, null),
        'counterOffer' => $faker->randomDigit,
        'currency' => $faker->currencyCode,
        'transitDays' => $faker->randomDigit,
        'visibleToSellersIds' => $faker->boolean(),
        'visibleToSellersNames' => $faker->sentence,

    ];
});