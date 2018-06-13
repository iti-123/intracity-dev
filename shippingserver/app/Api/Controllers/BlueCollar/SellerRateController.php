<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace Api\Controllers\BlueCollar;

use Illuminate\Http\Request;
use Api\Controllers\BaseController;
use Api\Requests\BlueCollar\SellerPostRequest;
use Api\Services\BlueCollar\SellerService;
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
