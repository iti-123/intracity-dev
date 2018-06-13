<?php

namespace ApiV2\Services;


class CurrencyConvertion
{

    public static function get($from, $to, $fromAmount)
    {

        return $fromAmount * 70;

    }

}