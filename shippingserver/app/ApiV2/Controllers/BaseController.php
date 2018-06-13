<?php

namespace ApiV2\Controllers;

use ApiV2\Requests\BaseShippingResponse as resp;
use App\Exceptions\ApplicationException;
use App\Exceptions\ServiceException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ValidationException;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;
use Log;

class BaseController extends Controller
{
    use Helpers;


    protected function errorResponse(\Exception $e)
    {

        $debugInfo = "";

        if (env('APP_DEBUG') == "true") {
            $debugInfo = $e->getMessage() . ' => ' . $e->getTraceAsString();
        }

        if ($e instanceof ServiceException) {

            return resp::failed([], $debugInfo);

        } else if ($e instanceof UnauthorizedException) {

            return resp::unauthorized([], $debugInfo);

        } else if ($e instanceof ValidationException) {

            $validationException = $e;
            return resp::bad([], $validationException->errors, $debugInfo);

        } else if ($e instanceof ApplicationException) {

            return resp::failed([], $debugInfo);

        } else {

            LOG::error("Request failed", (array)$e->getMessage());

            return resp::failed([], $debugInfo);

        }


    }
}