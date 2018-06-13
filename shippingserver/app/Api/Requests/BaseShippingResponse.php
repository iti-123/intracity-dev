<?php
/**
 * Created by PhpStorm.
 * User: shivaram
 * Date: 2/5/17
 * Time: 12:02 AM
 */

namespace Api\Requests;


/**
 * Class BaseShippingResponse
 * This is the base class for all responses of Shipping API.
 * @package Api\Requests
 */
class BaseShippingResponse
{

    const NOT_AUTHORIZED = "E403";
    const BAD_REQUEST = "E400";
    const REQUEST_FAILED = "E500";

//    const STANDARD_ERRORS = [
//             self::NOT_AUTHORIZED => "Not authorized to perform this action",
//             self::BAD_REQUEST => "The request is not valid. Please correct the errors and try again",
//             self::REQUEST_FAILED => "Request could not be fulfilled. Please try again or contact the administrator"
//    ];

    /**
     * Is this a successful response?
     * @var $isSuccessful
     */
    public $isSuccessful;


    /**
     * List of errors
     * @var array
     */
    public $errors = [];

    /**
     * Additional context to augment the error information.
     * @var array $context
     */
    public $context = [];

    /**
     * The response payload, for successful requests.
     * @var $payload
     */
    public $payload;


    /** Additional debug info for the client. Will be set based on environment setting APP_DEBUG */
    public $debugInfo;

    /**
     * BaseShippingResponse constructor.
     * @param $isSuccessful
     * @param array $context
     * @param $payload
     * @param array $errors
     */
    public function __construct($isSuccessful, array $context = [], $payload, $errors, $debugInfo = "")
    {
        $this->isSuccessful = $isSuccessful;
        $this->errors = $errors;
        $this->context = $context;
        $this->payload = $payload;
        $this->debugInfo = $debugInfo;
    }

    public static function ok($payload)
    {
        return (response()->json(new BaseShippingResponse(true, [], $payload, [])));
    }

    public static function bad($context, $errors = [], $debugInfo = "")
    {
        return (response()->json(new BaseShippingResponse(false, $context, "", $errors, $debugInfo), 200));
    }

    public static function unauthorized($context, $debugInfo = "")
    {
        return (response()->json(
            new BaseShippingResponse(false, $context, "", [
                // self::NOT_AUTHORIZED => [  self::STANDARD_ERRORS[self::NOT_AUTHORIZED]  ]
            ], $debugInfo

            ), 200)
        );
    }

    public static function failed($context, $debugInfo = "")
    {
        return (response()->json(
            new BaseShippingResponse(false, $context, "", [
                // self::REQUEST_FAILED => [  self::STANDARD_ERRORS[self::REQUEST_FAILED]  ]
            ], $debugInfo

            ), 200)
        );
    }
}