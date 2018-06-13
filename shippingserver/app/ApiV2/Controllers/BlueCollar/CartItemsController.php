<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\BlueCollar;
use ApiV2\Controllers\BaseController;
use ApiV2\Requests\BlueCollar\BuyerNegotiationRequest;
use ApiV2\Requests\BlueCollar\BuyerPostMasterRequest;
use ApiV2\Requests\BlueCollar\BuyerPostRequest;
use ApiV2\Services\BlueCollar\BuyerService;
use Exception;
use Response;

class CartItemsController extends BaseController
{
    public function addInitialCartDetails(BuyerPostRequest $request)
    {
       return "hello";
    }

}
