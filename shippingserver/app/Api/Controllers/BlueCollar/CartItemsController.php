<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace Api\Controllers\BlueCollar;
use Api\Controllers\BaseController;
use Api\Requests\BlueCollar\BuyerNegotiationRequest;
use Api\Requests\BlueCollar\BuyerPostMasterRequest;
use Api\Requests\BlueCollar\BuyerPostRequest;
use Api\Services\BlueCollar\BuyerService;
use Exception;
use Response;

class CartItemsController extends BaseController
{
    public function addInitialCartDetails(BuyerPostRequest $request)
    {
       return "hello";
    }

}
