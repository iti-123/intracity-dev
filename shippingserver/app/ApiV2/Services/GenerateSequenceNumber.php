<?php

namespace ApiV2\Services;

use Carbon\Carbon;

class GenerateSequenceNumber
{

    const SEQ_NUM_LENGTH = 6,
        STR_PAD = STR_PAD_LEFT,
        STR = "0";

    public static function get($serviceName, $uniqId)
    {

        $seqNum = $serviceName
            . "/"
            . Carbon::now()->year
            . "/"
            . str_pad($uniqId, self::SEQ_NUM_LENGTH, self::STR, self::STR_PAD);

        return $seqNum;

    }

}