<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 05-07-2017
 * Time: 19:37
 */

namespace Api\Controllers\Hyperlocal;


use Api\Controllers\BaseController;
use Api\Model\HyperLocal\Seller_search;
use Illuminate\Http\Request;

//use Api\Requests\Hyperlocal\BuyerPostRequest;
//use Api\Services\BlueCollar\SellerRegistrationService;


class SellerSearch extends BaseController
{

    public function searchresult(Request $request)
    {

        $search_data = $request->input();
        Seller_search::search_result($search_data);
    }
}