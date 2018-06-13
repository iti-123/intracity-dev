<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 4/19/17
 * Time: 11:27 PM
 */

namespace ApiV2\Controllers;

use ApiV2\Requests\BaseShippingResponse as resp;
use App\Exceptions\ApplicationException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationBuilder;

class HowtoExceptionsController extends BaseController
{

    public function good()
    {
        return resp::ok("This is a good request and a successful response");
    }

    public function ugly()
    {

        try {

            throw new \Exception("Failed with an unknown exception");

        } catch (\Exception $e) {

            return $this->errorResponse($e);
        }

    }

    public function bad()
    {

        try {

            ValidationBuilder::create()
                ->error("field1", "value1 is not a number")
                ->error("field2", "value2 must be a date")
                ->errorMany("field3", ["value3 not in codelist", "value3 must not be empty"])
                ->error("field4", "value4 is null")
                ->errorMany("field5", ["value5 is not a valid email", "value5 is of type string"])
                ->raise();

        } catch (\Exception $e) {

            return $this->errorResponse($e);
        }

    }

    public function unauthorized()
    {

        try {

            throw new UnauthorizedException("buyer2 is not the owner of the post 22");

        } catch (\Exception $e) {

            return $this->errorResponse($e);
        }

    }

    public function failure()
    {

        try {

            throw new ApplicationException(["ctx1" => "val1", "ctx2" => "val2"], ["buyer3 is not ready yet"]);

        } catch (\Exception $e) {

            return $this->errorResponse($e);
        }

    }

}