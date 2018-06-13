<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/5/17
 * Time: 12:00 AM
 */

namespace App\Exceptions;


class ApplicationException extends \Exception
{

    public $appErrors = [];

    public $context = [];

    /**
     * ApplicationException constructor.
     * @param array $appErrors
     * @param array $context
     */
    public function __construct($context, $appErrors)
    {

        $this->appErrors = $appErrors;
        $this->context = $context;
    }


    /**
     * Fluent API to register more validation error messages
     * @param $errorCode
     * @param $errorMessage
     */
    public function add($errorCode, $errorMessage)
    {

        $this->appErrors[$errorCode] = $errorMessage;
        return $this;

    }

    /**
     * Fluent API to register more validation error messages
     * @param $errorCode
     * @param $errorMessage
     */
    public function addAll($validationErrors)
    {

        $this->appErrors = $validationErrors;
        return $this;

    }
}