<?php

namespace ApiV2\Framework;

use ApiV2\Utils\DateUtils;
use Validator;

class ShpValidator extends Validator
{

    public static function validate($obj, $rules, $customMsgs)
    {

        $requestArray = $array = json_decode(json_encode($obj), true);

        self::extend('valid_from_check', function ($attribute, $value, $parameters, $validator) {
            $yestDate = DateUtils::getYesterdayDate();
            if ($value < $yestDate) {
                return False;
            }
            return True;
        });

        $validator = self::make($requestArray, $rules, $customMsgs);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }
        return [];
    }
}