<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\BlueCollar;


use ApiV2\Controllers\BaseController;
use ApiV2\Services\BlueCollar\SellerVerificationService;
use Exception;
use Illuminate\Http\Request;
use Response;

class SellerVerification extends BaseController
{
    public function getSellerData(Request $request)
    {
        $response = SellerVerificationService::getSellerData($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function getAllUnverified()
    {
        $response = SellerVerificationService::getAllUnverified();
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function sellerVerify(Request $request)
    {
        $response = SellerVerificationService::sellerVerify($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }
}
