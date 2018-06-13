<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\BlueCollar;


use ApiV2\Controllers\BaseController;
use ApiV2\Services\BlueCollar\SellerPostService;
use Exception;
use Response;

class SellerPostController extends BaseController
{
    public function postDetails($id)
    {
        //return $id;
        $response = SellerPostService::postDetails($id);
        return Response::json($response);
        try {
        } catch (Exception $e) {
            //return $e->message();
        }
    }

    public function postDetailPage($id)
    {
        $response = SellerPostService::postDetailPage($id);
        return Response::json($response);
    }
}
