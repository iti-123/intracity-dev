<?php

namespace Api\Services;


class CurrencyConvertion
{

    public static function get($from, $to, $fromAmount)
    {

        return $fromAmount * 70;

    }

}