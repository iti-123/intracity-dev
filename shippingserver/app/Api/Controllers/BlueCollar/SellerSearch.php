<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace Api\Controllers\BlueCollar;


use Api\Controllers\BaseController;
use Api\Requests\BlueCollar\SellerSearchRequest;
use Api\Services\BlueCollar\SellerSearchService;
use Exception;
use Illuminate\Http\Request;
use Response;

class SellerSearch extends BaseController
{
    public function locationSearch(Request $request)
    {
        $response = SellerSearchService::locationSearch($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function search(SellerSearchRequest $request)
    {
        $response = SellerSearchService::search($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function sellerSearchDetails(Request $request)
    {
        $response = SellerSearchService::sellerDetails($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }
}
