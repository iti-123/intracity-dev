<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/9/17
 * Time: 2:05 PM
 */

namespace Api\Services;


class ServiceErrors
{

    static $errorPrefixes = array(

        //BuyerPostService : Error class 100

        "BuyerPostService" => "BPS",
        "SellerPostService" => "SPS",
        "PaymentService" => "PS",
        "OrderService" => "OS",
        "FulfillmentService" => "FS",

        //Cross functional Services
        "LocationService" => "LS",
        "CodelistService" => "CS",
        "DocumentService" => "DS",
        "EmailService" => "ES",
        "NotificationService" => "NS",
        "SmsService" => "SS",
        "InternationalizationService" => "IS"
    );


    static function getPrefix($serviceName)
    {

        return ServiceErrors::$errorPrefixes[$serviceName];
    }

}