<?php
/**
 * Created by PhpStorm.
 * User: sainath
 * Date: 4/10/17
 * Time: 9:17 PM
 */

namespace Api\Services;


class ServiceTaxCharges
{

    public static $serviceTaxPercent = 15;

    public static function getServiceTaxAmount($totalAmount)
    {

        return (($totalAmount * self::$serviceTaxPercent) / 100);

    }

}