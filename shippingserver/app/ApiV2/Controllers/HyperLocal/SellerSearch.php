<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 05-07-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\Hyperlocal;


use ApiV2\Controllers\BaseController;
use ApiV2\Model\HyperLocal\Seller_search;
use Illuminate\Http\Request;

//use ApiV2\Requests\Hyperlocal\BuyerPostRequest;
//use ApiV2\Services\BlueCollar\SellerRegistrationService;


class SellerSearch extends BaseController
{

    public function searchresult(Request $request)
    {

        $search_data = $request->input();
        Seller_search::search_result($search_data);
    }
}