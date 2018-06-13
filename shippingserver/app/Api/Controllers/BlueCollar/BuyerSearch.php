<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace Api\Controllers\BlueCollar;


use Api\Controllers\BaseController;
use Api\Requests\BlueCollar\BuyerSearchRequest;
use Api\Services\BlueCollar\BuyerSearchService;
use Exception;
use Illuminate\Http\Request;
use Response;

class BuyerSearch extends BaseController
{
    public function locationSearch(Request $request)
    {
        $response = BuyerSearchService::locationSearch($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function search(BuyerSearchRequest $request)
    {
        $response = BuyerSearchService::search($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function sellerDetails(Request $request)
    {
        $response = BuyerSearchService::sellerDetails($request);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }
}
