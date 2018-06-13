<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\BlueCollar;

use Illuminate\Http\Request;
use ApiV2\Controllers\BaseController;
use ApiV2\Requests\BlueCollar\SellerPostRequest;
use ApiV2\Services\BlueCollar\SellerService;
use Exception;
use Response;

class SellerRateController extends BaseController
{
    public function post(Request $request,$status)
    {
        $response = SellerService::post($request,$status);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }
}
