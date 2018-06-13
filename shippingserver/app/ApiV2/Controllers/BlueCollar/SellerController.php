<?php
/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 07-02-2017
 * Time: 19:37
 */

namespace ApiV2\Controllers\BlueCollar;


use ApiV2\Controllers\BaseController;
use App\Exceptions\ApplicationException;
use Illuminate\Http\Request;
use Log;
use Response;
use Exception;
use Storage;
use DB;
use ApiV2\Services\BlueCollar\SellerService;
use ApiV2\Requests\BlueCollar\SellerPostRequest;
use ApiV2\Requests\BlueCollar\SellerPostMasterRequest;
use ApiV2\Requests\BlueCollar\SellerNegotiationRequest;

class SellerController extends BaseController
{
    public function emailCheck(Request $request){
      $response = SellerService::emailCheck($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }
    
    public function post(SellerPostRequest $request){
      $response = SellerService::post($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function postList(SellerPostMasterRequest $request){
      $response = SellerService::postList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }
    
    public function boundCount(){
      $response = SellerService::boundCount();
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }
    
    public function inboundList(SellerPostMasterRequest $request){
      $response = SellerService::inboundList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function outboundList(SellerPostMasterRequest $request){
      $response = SellerService::outboundList($request);
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }

    public function quoteAction(SellerNegotiationRequest $request){
      switch ($request->action) {
        case 'INITIALISING':
          $response = SellerService::sellerQuoteInitialising($request);
          break;
        case 'OFFER':
          $response = SellerService::sellerQuoteOffer($request);
          break;
        case 'COUNTER':
          $response = SellerService::sellerQuoteCounter($request);
          break;
        case 'ACCEPT':
          $response = SellerService::sellerQuoteAccept($request);
          break;
        case 'DENY':
          $response = SellerService::sellerQuoteDeny($request);
          break;
      }
      return Response::json($response);
      try{
      }catch(Exception $e){
        //return $e->message();
      }
    }
}
