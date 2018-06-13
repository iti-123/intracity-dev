<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/17/17
 * Time: 12:10 AM
 */

namespace Api\Utils;


class ArrayUtils
{

    /**
     * Is this an associative array ?
     * Reference : http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
     */
    public static function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }


    /**
     * Method to push into associative array
     * @param $array
     * @param $key
     * @param $value
     * @return mixed
     * To use this $myarray = array_push_assoc($myarray, 'h', 'hello');
     */
    public static function array_push_assoc($array, $key, $value)
    {
        $array[$key] = $value;
        return $array;
    }

}