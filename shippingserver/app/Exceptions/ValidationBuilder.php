<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/19/17
 * Time: 1:38 PM
 */

namespace App\Exceptions;

use Log;


/**
 * Class ValidationBuilder supports building capturing validation errors
 * using a FluentAPI style
 * @package App\Exceptions
 */
class ValidationBuilder
{

    private $errors = [];

    /**
     * Create a new Validation builder
     * @return ValidationBuilder
     */
    public static function create()
    {
        return new ValidationBuilder();
    }

    /**
     * Append one or more validation errors
     * @param $context
     * @param array $validationErrors
     * @return $this
     */
    public function errorMany($key, $errors = [])
    {

        if (!is_string($key) || count($errors) <= 0) {
            LOG::warning("ValidationBuilder :: error(key, error) : key has to be of type string and atleast one error must be specified");
        } else {
            array_push($this->errors, ["key" => $key, "values" => $errors]);
        }

        return $this;
    }


    public function errorLaravel($laravelValidationErrors = [])
    {

        if (count($laravelValidationErrors) > 0) {

            foreach ($laravelValidationErrors as $key => $value) {
                array_push($this->errors, ["key" => $key, "values" => $value]);
            }

        }

        return $this;
    }

    public function error($key, $error)
    {

        if (!is_string($key) || !is_string($error)) {
            LOG::warning("ValidationBuilder :: error(key, error) : key and error have to be of type string");
        } else {
            array_push($this->errors, ["key" => $key, "values" => [$error]]);
        }

        return $this;
    }


    public function get()
    {
        return new ValidationException($this->errors);
    }

    public function raise()
    {

        if (count($this->errors) > 0) {
            throw new ValidationException($this->errors);
        }

    }

}