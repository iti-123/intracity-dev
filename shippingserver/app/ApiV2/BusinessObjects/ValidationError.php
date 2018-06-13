<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/8/17
 * Time: 7:39 PM
 */

namespace ApiV2\BusinessObjects;


class ValidationError
{

    protected $errorCode;

    protected $errorMessage;

    /**
     * ValidationError constructor.
     * @param $errorCode
     * @param $errorMessage
     */
    public function __construct($errorCode, $errorMessage)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return 'Validation Error : ' . $this->errorCode . ' => ' . $this->errorMessage;
    }


}