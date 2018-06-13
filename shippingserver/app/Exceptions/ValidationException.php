<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/19/17
 * Time: 1:32 PM
 */

namespace App\Exceptions;


class ValidationException extends \Exception
{

    public $errors = [];

    /**
     * ValidationException constructor.
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }


}