<?php
/**
 * Created by PhpStorm.
 * User: 10645
 * Date: 1/31/2017
 * Time: 6:38 PM
 */

namespace App\Exceptions;

class CustomException extends \Exception
{
    final public static function getCustomException($e)
    {

        //return $e;//app('Illuminate\Http\Response')->status();
        return $e->getMessage();
    }
}